@foreach($doctors as $k=>$e)
<tr>
    <th scope="row"> {{$k+$doctors->firstItem()}}</th>
    <td class="text-capitalize">{{$e->name ?? 'N/A'}}</td>
    <td >{{$e->email ?? 'N/A'}}</td>
    <td>{{$e->mobile_number ?? 'N/A'}}</td>
    <td>
        <a class="btn btn-sm btn-white"
            href="{{route('admin.doctor.edit',[$e->id])}}" title="{{__('messages.edit')}}"><i class="tio-edit"></i>
        </a>
        <a class="btn btn-sm btn-danger" href="javascript:"
            onclick="form_alert('advisor-{{$e->id}}','{{__('messages.Want_to_delete_this_role')}}')" title="{{__('messages.delete')}} "><i class="tio-delete-outlined"></i>
        </a>
        <form action="{{route('admin.doctor.delete',[$e->id])}}"
                method="post" id="doctor-{{$e->id}}">
            @csrf @method('delete')
        </form>
        <a class="btn btn-sm btn-white" href="{{ route('admin.doctor.view', [$e->id]) }}" 
           title="{{ __('messages.bankDetailsview') }}">
            <i class="tio-visible"></i>
        </a>
    </td>
</tr>
@endforeach