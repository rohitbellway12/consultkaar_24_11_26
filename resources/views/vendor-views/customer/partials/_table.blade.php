@foreach($customers as $k=>$e)
<tr>
    <th scope="row">{{$startIndex + $loop->iteration}}</th>
    <td class="text-capitalize">{{$e->name ?? 'N/A'}}</td>
    <td >{{$e->email ?? 'N/A'}}</td>
    <td>{{$e->mobile_number ?? 'N/A'}}</td>
    <td>
        <a class="btn btn-sm btn-danger" href="javascript:"
        onclick="form_alert('customer-{{$e->id}}','Want to delete this patient ?')" title="{{__('messages.delete')}}"><i class="tio-delete-outlined"></i>
    </a>
        <form action="{{route('admin.patient.delete',[$e->id])}}"
                method="post" id="customer-{{$e->id}}">
            @csrf @method('delete')
        </form>
    </td>
</tr>
@endforeach