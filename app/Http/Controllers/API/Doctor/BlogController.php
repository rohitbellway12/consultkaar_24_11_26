<?php 


namespace App\Http\Controllers\API\Doctor;

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
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'title'   => 'required',
    //         'content' => 'required',
    //         'image'   => 'nullable|image'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $validator->errors()->first()
    //         ]);
    //     }

    //     $imageName = null;

    //     if ($request->hasFile('image')) {
    //         $imageName = time() . '.' . $request->image->getClientOriginalExtension();
    //         $request->image->storeAs('blogs', $imageName, 'public');
    //     }

    //     $blog = Blog::create([
    //         'user_id' => auth()->id(),
    //         'title'   => $request->title,
    //         'content' => $request->content,
    //         'image'   => $imageName,
    //         'status'  => 'pending'
    //     ]);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Blog submitted for admin approval',
    //         'data' => $blog
    //     ]);
    // }
    
     public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required',
            'content' => 'required',
            'image'   => 'nullable|image'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $imageName = null;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->storeAs('blogs', $imageName, 'public');
        }

        $blog = Blog::create([
            'user_id' => auth()->id(),
            'title'   => $request->title,
            'content' => $request->content,
            'image'   => $imageName,
            'status'  => 'approved'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Blog posted successfully',
            'data' => $blog
        ]);
    }

    public function myBlogs()
    {
        try {
            // Get logged-in doctor with relationships in one query
            $doctor = User::with(['speciality', 'domain'])
                ->find(auth()->id());
                
            if (!$doctor) {
                return response()->json([
                    'status' => false,
                    'message' => 'Doctor not found'
                ], 404);
            }
            
            // Get followers count
            $followersCount = $this->getFollowersCount($doctor->id);
            
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

            $blogs = Blog::where('user_id', $doctor->id)
                ->latest()
                ->get()
                ->map(function($blog) use ($doctor, $doctorImageUrl, $profession, $domainInfo, $followersCount) {
                    return [
                        'id' => $blog->id,
                        'title' => $blog->title,
                        'content' => $blog->content,
                        'status' => $blog->status,
                        'created_at' => $blog->created_at->toDateTimeString(),
                        'image' => $blog->image ? $this->getBlogImageUrl($blog->image) : null,
                        'rejection_reason' => $blog->rejection_reason,
                        // ✅ Doctor details added here - all from user table via relationships
                        'doctor' => [
                            'id' => $doctor->id,
                            'name' => $doctor->name, // Automatically "Dr." removed via accessor
                            'image' => $doctorImageUrl,
                            'profession' => $profession,
                            'followers_count' => $followersCount,
                            'domain' => $domainInfo
                        ]
                    ];
                });

            return response()->json([
                'status' => true,
                'data' => $blogs
            ]);

        } catch (\Exception $e) {
            Log::error('myBlogs API Error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            // Get blog with user relationships in one query
            $blog = Blog::with(['user.speciality', 'user.domain'])
                ->where('id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$blog) {
                return response()->json([
                    'status' => false,
                    'message' => 'Blog not found'
                ]);
            }

            if ($blog->status === 'approved') {
                return response()->json([
                    'status' => false,
                    'message' => 'Approved blog cannot be edited'
                ]);
            }
            
            $doctor = $blog->user;
            
            // Get followers count
            $followersCount = $this->getFollowersCount($doctor->id);
            
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

            $blogData = [
                'id' => $blog->id,
                'title' => $blog->title,
                'content' => $blog->content,
                'status' => $blog->status,
                'created_at' => $blog->created_at->toDateTimeString(),
                'image' => $blog->image ? $this->getBlogImageUrl($blog->image) : null,
                'rejection_reason' => $blog->rejection_reason,
                // ✅ Doctor details added here - all from user table
                'doctor' => [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'image' => $doctorImageUrl,
                    'profession' => $profession,
                    'followers_count' => $followersCount,
                    'domain' => $domainInfo
                ]
            ];

            return response()->json([
                'status' => true,
                'data' => $blogData
            ]);

        } catch (\Exception $e) {
            Log::error('editBlog API Error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$blog) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found'
            ]);
        }

        if ($blog->status === 'approved') {
            return response()->json([
                'status' => false,
                'message' => 'Approved blog cannot be updated'
            ]);
        }

        $blog->update([
            'title'   => $request->title ?? $blog->title,
            'content' => $request->content ?? $blog->content,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Blog updated successfully',
            'data' => $blog
        ]);
    }

    public function destroy($id)
    {
        $blog = Blog::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$blog) {
            return response()->json([
                'status' => false,
                'message' => 'Blog not found'
            ]);
        }

        // if ($blog->status === 'approved') {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Approved blog cannot be deleted'
        //     ]);
        // }

        $blog->delete();

        return response()->json([
            'status' => true,
            'message' => 'Blog deleted successfully'
        ]);
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
     * Helper: Blog ki image URL generate karega
     */
    private function getBlogImageUrl($imageName)
    {
        return (env('APP_ENV') == 'local') 
            ? asset('storage/blogs/' . $imageName) 
            : asset('storage/app/public/blogs/' . $imageName);
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