<?php 
// namespace App\Http\Controllers\API;

// use App\Http\Controllers\Controller;
// use App\Models\Blog;
// use Illuminate\Http\Request;
// use Validator;
// use Illuminate\Support\Facades\Storage;

// class BlogController extends Controller
// {
    
 

// public function Blogs()
// {
//     $blogs = Blog::where('status', 'approved')
//         ->latest()
//         ->get()
//         ->map(function($blog) {
//             return [
//                 'id' => $blog->id,
//                 'title' => $blog->title,
//                 'content' => $blog->content,
//                 'status' => $blog->status,
//                 'created_at' => $blog->created_at->toDateTimeString(),
//                 'image' => $blog->image ? asset('storage/app/public/blogs/'.$blog->image) : null,
//             ];
//         });

//     return response()->json([
//         'status' => true,
//         'data' => $blogs
//     ]);
// }





    
// }

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\User;
use App\Models\Speciality;
use App\Models\Domain;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    public function Blogs()
    {
        try {
            // Get all approved blogs with their user relationships
            $blogs = Blog::with(['user.speciality', 'user.domain'])
                ->where('status', 'approved')
                ->latest()
                ->get()
                ->map(function($blog) {
                    $doctor = $blog->user;
                    
                    // Skip if doctor not found
                    if (!$doctor) {
                        return null;
                    }
                    
                    // Get doctor's image URL
                    $doctorImageUrl = $this->getDoctorImageUrl($doctor);
                    
                    // Get profession name
                    $profession = $doctor->speciality ? $doctor->speciality->name : 'General Physician';
                    
                    // Get domain details if exists
                    $domainInfo = null;
                    if ($doctor->domain) {
                        $domainInfo = [
                            'id' => $doctor->domain->id,
                            'name' => $doctor->domain->name,
                            'description' => $doctor->domain->description ?? null,
                            'image' => $doctor->domain->image ? $this->getDomainImageUrl($doctor->domain->image) : null
                        ];
                    }
                    
                    // Get followers count
                    $followersCount = $this->getFollowersCount($doctor->id);
                    
                    // Format blog image URL
                    $blogImageUrl = null;
                    if (!empty($blog->image)) {
                        $blogImageUrl = (env('APP_ENV') == 'local')
                            ? asset('storage/blogs/' . $blog->image)
                            : asset('storage/app/public/blogs/' . $blog->image);
                    }

                    return [
                        'id' => $blog->id,
                        'title' => $blog->title,
                        'content' => $blog->content,
                        'status' => $blog->status,
                        'created_at' => $blog->created_at->format('d M Y'),
                        'image' => $blogImageUrl,
                        // ✅ Doctor details added here - all from user table
                        'doctor' => [
                            'id' => $doctor->id,
                            'name' => $doctor->name, // Automatically "Dr." removed via accessor
                            'image' => $doctorImageUrl,
                            'profession' => $profession,
                            'followers_count' => $followersCount,
                            'domain' => $domainInfo,
                
                             'custom_domain_name' => $doctor->custom_domain_name ?? null,
                       'custom_specialization' => $doctor->custom_specialization ?? null
                        ]
                    ];
                })
                ->filter() // Remove null values (blogs without doctors)
                ->values(); // Re-index the collection

            return response()->json([
                'status' => true,
                'message' => 'Blogs fetched successfully',
                'data' => $blogs
            ]);

        } catch (\Exception $e) {
            Log::error('Blogs API Error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }
    
    /**
     * Helper: Doctor ki profile image URL generate karega
     */
    private function getDoctorImageUrl($doctor)
    {
        if (!empty($doctor->image)) {
            return (env('APP_ENV') == 'local') 
                ? asset('storage/users/' . $doctor->image) 
                : asset('storage/app/public/users/' . $doctor->image);
        }
        
        $assetPrefixPath = (env('APP_ENV') == 'local') ? '' : 'public';
        return asset($assetPrefixPath . "/assets/admin/img/160x160/img1.jpg");
    }
    
    /**
     * Helper: Domain ki image URL generate karega
     */
    private function getDomainImageUrl($imageName)
    {
        if (empty($imageName)) return null;
        
        return (env('APP_ENV') == 'local') 
            ? asset('storage/domains/' . $imageName) 
            : asset('storage/app/public/domains/' . $imageName);
    }
    
    /**
     * Helper: Doctor ke followers count nikalega
     */
    private function getFollowersCount($doctorId)
    {
        return \App\Models\FavoriteDoctors::where('doctor_id', $doctorId)->count();
    }
}