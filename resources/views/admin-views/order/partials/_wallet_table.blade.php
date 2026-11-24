@foreach($withdrawal_requests as $key=>$withdrawal_request)

<tr class="status-{{$withdrawal_request['order_status']}} class-all">
<td class="">
{{$key + 1}}
</td>
<td class="table-column-pl-0">{{$withdrawal_request['id']}}</td>
<td>{{date('d M Y',strtotime($withdrawal_request['created_at']))}}</td>
<td>

@if($withdrawal_request->customer)
{{$withdrawal_request->customer['name']}} ({{$withdrawal_request->customer['mobile_number']}})


@else
<label class="badge badge-danger">{{__('messages.invalid')}} {{__('messages.customer')}} {{__('messages.data')}}</label>
@endif
</td>

<td>Rs. {{ number_format($withdrawal_request->amount)  }} </td>

<td class="text-capitalize">


<span class="badge badge-soft-info ml-2 ml-sm-3">
<span class="legend-indicator bg-info"></span>{{str_replace('_',' ',$withdrawal_request['status'])}}
</span>

</td>
<td>
@if($withdrawal_request['status'] == 'pending')
<a class="btn btn-sm btn-success manage-request" data-request-id="{{ $withdrawal_request['id'] }}" data-request-status='approve'>
<i class="tio-done"></i> Approve
</a>
<a class="btn btn-sm btn-danger manage-request" data-request-id="{{ $withdrawal_request['id'] }}" data-request-status='reject'>
<i class="tio-clear"></i> Reject
</a>
@endif

</td>
</tr>


@endforeach
