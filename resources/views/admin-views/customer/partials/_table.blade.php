@foreach($user_list as $k=>$e)
<tr>
    <th scope="row">{{$startIndex + $loop->iteration}}</th>
    <td class="text-capitalize">{{$e->name ?? 'N/A'}}</td>
    <td >{{$e->email ?? 'N/A'}}</td>
    <td>{{$e->mobile_number ?? 'N/A'}}</td>
     <td>{{$e->date_of_birth ?? 'N/A'}}</td>
                                    <td>{{$e->gender ?? 'N/A'}}</td>
                                    <td>{{$e->martial_status ?? 'N/A'}}</td>
                                    <td>{{$e->blood_group ?? 'N/A'}}</td>
                                    <td>{{$e->height ?? 'N/A'}}</td>
                                    <td>{{$e->allergies ?? 'N/A'}}</td>
                                    <td>{{$e->current_medications ?? 'N/A'}}</td>
                                  {{--  <td>{{$e->past_medications ?? 'N/A'}}</td> --}}
                                    <td>{{$e->injuries ?? 'N/A'}}</td>
                                    <td>{{$e->surgeries ?? 'N/A'}}</td>
                                    <td>{{$e->smoking_habits ?? 'N/A'}}</td>
                                    <td>{{$e->alchol_consumption ?? 'N/A'}}</td>
                                    <td>{{$e->activity_level ?? 'N/A'}}</td>
                                    <td>{{$e->food_preference ?? 'N/A'}}</td>
                                    <td>{{$e->occupation ?? 'N/A'}}</td>
                               {{--     <td>{{$e->qualification ?? 'N/A'}}</td>
                                    <td>{{$e->experience ?? 'N/A'}}</td>  --}}
                                    <td>{{$e->address ?? 'N/A'}}</td>
                                    <td>{{$e->city ?? 'N/A'}}</td>
                                    <td>{{$e->state ?? 'N/A'}}</td>
    <td>
        <a class="btn btn-sm btn-danger" href="javascript:"
        onclick="form_alert('customer-{{$e->id}}','Want to delete this customer ?')" title="{{__('messages.delete')}}"><i class="tio-delete-outlined"></i>
    </a>
        <form action="{{route('admin.patient.delete',[$e->id])}}"
                method="post" id="customer-{{$e->id}}">
            @csrf @method('delete')
        </form>
    </td>
</tr>
@endforeach