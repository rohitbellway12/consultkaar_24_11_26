@extends('layouts.admin.app')

@section('content')
    <div class="content container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-uppercase">Relevant Documents List</h4>
            <a href="{{ route('admin.degree.add-new') }}" class="btn btn-primary">Add New Document</a>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Document Name</th>
                            <th>Language</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($degrees as $degree)
                            <tr>
                                <td>{{ $degree->id }}</td>
                                <td>{{ $degree->name }}</td>
                                <td>{{ strtoupper($degree->lang_code) }}</td>
                                <td><img src="{{ asset('storage/app/public/degree/' . $degree->image) }}" width="110"></td>
                                <td>
                                    <a href="{{ route('admin.degree.edit', $degree->id) }}"
                                        class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('admin.degree.delete', $degree->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this degree?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
