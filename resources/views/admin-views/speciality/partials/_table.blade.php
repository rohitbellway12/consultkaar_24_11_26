@foreach ($specialities as $key => $speciality)
    <tr>
        <td>{{ $key + 1 }}</td>
        <td>
            <span class="d-block font-size-sm text-body">
                {{ $speciality['name'] }}
            </span>
        </td>
        <td>
            <span class="d-block font-size-sm text-body">
                {{ $speciality->domain->name ?? 'N/A' }}
            </span>
        </td>
        <?php
        
        $specialityImagePath = env('APP_ENV') == 'local' ? asset('storage/speciality/' . $speciality->image) : asset('storage/app/public/speciality/' . $speciality->image);
        ?>
        <td>
            <img class="avatar avatar-lg mr-3" src="{{ $specialityImagePath }}" alt="{{ $speciality->name }} image">

        </td>
        <td>
            <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{ $speciality->id }}">
                <input type="checkbox"
                    onclick="location.href='{{ route('admin.speciality.status', [$speciality['id'], $speciality->status ? 0 : 1]) }}'"class="toggle-switch-input"
                    id="stocksCheckbox{{ $speciality->id }}" {{ $speciality->status ? 'checked' : '' }}>
                <span class="toggle-switch-label">
                    <span class="toggle-switch-indicator"></span>
                </span>
            </label>
        </td>
        <td>
            <a class="btn btn-sm btn-white" href="{{ route('admin.speciality.edit', [$speciality['id']]) }}"
                title="{{ __('messages.edit') }} {{ __('messages.speciality') }}"><i class="tio-edit"></i>
            </a>
            <a class="btn btn-sm btn-white" href="javascript:"
                onclick="form_alert('speciality-{{ $speciality['id'] }}','Want to delete this speciality ?')"
                title="{{ __('messages.delete') }} {{ __('messages.speciality') }}"><i
                    class="tio-delete-outlined"></i>
            </a>
            <form action="{{ route('admin.speciality.delete', [$speciality['id']]) }}" method="post"
                id="speciality-{{ $speciality['id'] }}">
                @csrf @method('delete')
            </form>
        </td>
    </tr>
@endforeach
