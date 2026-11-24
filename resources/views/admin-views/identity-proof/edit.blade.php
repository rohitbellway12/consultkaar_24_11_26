@extends('layouts.admin.app')

@section('content')
    <div class="content container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-uppercase">Edit Identity Proof</h4>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.identity-proof.update', $identityProof->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="lang_code">Language</label>
                        <select name="lang_code" id="lang_code" class="form-control">
                            <option value="en" {{ $identityProof->lang_code == 'en' ? 'selected' : '' }}>English
                            </option>
                            {{--  <option value="fr" {{ $identityProof->lang_code == 'fr' ? 'selected' : '' }}>French</option> --}}
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="name_en">Name (English)</label>
                        <input type="text" name="name_en" id="name_en" class="form-control"
                            value="{{ $identityProof->name }}">
                    </div>

                    <div class="form-group">
                        <label for="image">Icon</label>
                        <input type="file" name="image" id="image" class="form-control">
                        <img src="{{ asset('storage/app/public/identity_proof/' . $identityProof->image) }}" width="200"
                            class="mt-2">
                    </div>

                    <button type="submit" class="btn btn-success">Update Identity Proof</button>
                </form>
            </div>
        </div>
    </div>
@endsection
