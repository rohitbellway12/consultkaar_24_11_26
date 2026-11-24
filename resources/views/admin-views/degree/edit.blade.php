@extends('layouts.admin.app')

@section('content')
    <div class="content container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-uppercase">Edit Document</h4>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.degree.update', $degree->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="lang_code">Language</label>
                        <select name="lang_code" id="lang_code" class="form-control">
                            <option value="en" {{ $degree->lang_code == 'en' ? 'selected' : '' }}>English</option>
                            {{--  <option value="fr" {{ $degree->lang_code == 'fr' ? 'selected' : '' }}>French</option> --}}
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="name_en">Document Name (English)</label>
                        <input type="text" name="name_en" id="name_en" class="form-control"
                            value="{{ $degree->name }}">
                    </div>

                    {{--  <div class="form-group">
                    <label for="name_fr">Name (French)</label>
                    <input type="text" name="name_fr" id="name_fr" class="form-control" value="{{ $degree->name }}">
                </div> --}}

                    <div class="form-group">
                        <label for="image">Document Icon</label>
                        <input type="file" name="image" id="image" class="form-control">
                        <img src="{{ asset('storage/app/public/degree/' . $degree->image) }}" width="200" class="mt-2">
                    </div>

                    <button type="submit" class="btn btn-success">Update Document</button>
                </form>
            </div>
        </div>
    </div>
@endsection
