<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RelevantDocumentType;
use App\Models\Domain;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class RelevantDocumentTypeController extends Controller
{
    public function index(Request $request)
    {
        $searchKey = $request->input('search');
        
        $query = RelevantDocumentType::with('domain');
        
        if ($searchKey) {
            $query->where('name', 'like', "%{$searchKey}%");
        }
        
        $documents = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        $domains = Domain::all();
        
        return view('admin-views.relevant-document-type.index', compact('documents', 'domains'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'nullable|string|max:500',
            'domain_id' => 'nullable|exists:domains,id',
        ]);

        $document = new RelevantDocumentType();
        $document->name = $request->name;
        $document->description = $request->description;
        $document->domain_id = $request->domain_id;
        $document->lang_code = $request->lang_code ?? 'en';
        $document->status = 1;
        $document->save();

        Toastr::success('Relevant Document Type added successfully');
        return redirect()->route('admin.relevant-document-type.add-new');
    }

    public function edit($id)
    {
        $document = RelevantDocumentType::find($id);
        if (!$document) {
            Toastr::error('Document type not found');
            return back();
        }
        
        $domains = Domain::all();
        return view('admin-views.relevant-document-type.edit', compact('document', 'domains'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'description' => 'nullable|string|max:500',
            'domain_id' => 'nullable|exists:domains,id',
        ]);

        $document = RelevantDocumentType::find($id);
        if (!$document) {
            Toastr::error('Document type not found');
            return back();
        }

        $document->name = $request->name;
        $document->description = $request->description;
        $document->domain_id = $request->domain_id;
        $document->lang_code = $request->lang_code ?? 'en';
        $document->save();

        Toastr::success('Relevant Document Type updated successfully');
        return redirect()->route('admin.relevant-document-type.add-new');
    }

    public function status($id, $status)
    {
        $document = RelevantDocumentType::find($id);
        if ($document) {
            $document->status = $status;
            $document->save();
            Toastr::success('Status updated successfully');
        } else {
            Toastr::error('Document type not found');
        }
        return back();
    }

    public function delete($id)
    {
        $document = RelevantDocumentType::find($id);
        if ($document) {
            $document->delete();
            Toastr::success('Deleted successfully');
        } else {
            Toastr::error('Document type not found');
        }
        return back();
    }

    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $documents = RelevantDocumentType::with('domain')
            ->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                    $q->orWhere('description', 'like', "%{$value}%");
                }
            })
            ->orderBy('id', 'desc')
            ->get();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $items = collect($documents);
        $currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->all();
        $documents = new LengthAwarePaginator($currentPageItems, count($items), $perPage);
        $documents->withPath($request->url());

        return response()->json([
            'view' => view('admin-views.relevant-document-type.partials._table', compact('documents'))->render(),
            'count' => $documents->count()
        ]);
    }
}
