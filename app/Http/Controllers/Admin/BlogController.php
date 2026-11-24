<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    // Your existing methods...
    
    /**
     * Display a listing of blogs for approval
     */
    public function requestList(Request $request)
    {
        $query = Blog::with('user');
        
        // Apply search filter
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        // Apply status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        $blogs = $query->latest()->paginate(10);
        
        return view('admin-views.blogs.request-list', compact('blogs'));
    }
    
    /**
     * Display the specified blog
     */
    public function view($id)
    {
        $blog = Blog::with('user')->findOrFail($id);
        return view('admin-views.blogs.view', compact('blog'));
    }
    
    /**
     * Approve a blog
     */
    public function approve($id)
    {
        $blog = Blog::findOrFail($id);
        $blog->status = 'approved';
        $blog->save();
        
        return redirect()->route('admin.blog.request-list')
            ->with('success', 'Blog has been approved successfully.');
    }
    
    /**
     * Reject a blog
     */
    public function reject(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);
        $blog->status = 'rejected';
        $blog->rejection_reason = $request->rejection_reason;
        $blog->save();
        
        return redirect()->route('admin.blog.request-list')
            ->with('success', 'Blog has been rejected.');
    }
    

public function destroy($id)
{
    try {
        \Log::info('Attempting to delete blog with ID: ' . $id); // Add this debug line
        
        $blog = Blog::findOrFail($id);
        $blog->delete();
        
        \Log::info('Blog deleted successfully: ' . $id); // Add this debug line
        
        return redirect()->back()->with('success', 'Blog deleted successfully.');
    } catch (\Exception $e) {
        \Log::error('Error deleting blog: ' . $e->getMessage()); // Add this debug line
        return redirect()->back()->with('error', 'Error deleting blog: ' . $e->getMessage());
    }
}
    
}