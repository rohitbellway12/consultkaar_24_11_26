@extends('layouts.admin.app')

@section('content')
    <div class="content container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-uppercase">Identity Proof List</h4>
            <a href="{{ route('admin.identity-proof.add-new') }}" class="btn btn-primary">Add New Identity Proof</a>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Language</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($identityProofs as $proof)
                            <tr>
                                <td>{{ $proof->id }}</td>
                                <td>{{ $proof->name }}</td>
                                <td>{{ strtoupper($proof->lang_code) }}</td>
                                <td><img src="{{ asset('storage/app/public/identity_proof/' . $proof->image) }}"
                                        width="110"></td>
                                <td>
                                    <a href="{{ route('admin.identity-proof.edit', $proof->id) }}"
                                        class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('admin.identity-proof.delete', $proof->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this identity proof?');">Delete</button>
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
