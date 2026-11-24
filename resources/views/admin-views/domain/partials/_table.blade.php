@foreach ($domains as $key => $domain)
    <tr>
        <td>{{ $key + 1 }}</td>
        <td>
            <span class="d-block font-size-sm text-body">
                {{ $domain['name'] }}
            </span>
        </td>
        <td>
            <a class="btn btn-sm btn-white" href="{{ route('admin.domain.edit', [$domain['id']]) }}"
                title="{{ __('messages.edit') }}"><i class="tio-edit"></i>
            </a>
            <a class="btn btn-sm btn-white" href="javascript:"
                onclick="form_alert('domain-{{ $domain['id'] }}','Want to delete this domain ?')"
                title="{{ __('messages.delete') }}"><i class="tio-delete-outlined"></i>
            </a>
            <form action="{{ route('admin.domain.delete', [$domain['id']]) }}" method="post"
                id="domain-{{ $domain['id'] }}">
                @csrf @method('delete')
            </form>
        </td>
    </tr>
@endforeach
