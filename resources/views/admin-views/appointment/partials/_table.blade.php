@foreach($orders as $key=>$order)
<tr class="status-{{$order['order_status']}} class-all">
    <td class="">
        {{$key + 1}}
    </td>
    <td class="table-column-pl-0">
        {{$order['id']}}
    </td>
    <td>{{date('d M Y',strtotime($order['created_at']))}}</td>
    <td>
        @if($order->customer)
            <a class="text-body text-capitalize"
               href="{{route('admin.customer.view',[$order['user_id']])}}">{{$order->customer['name']}}</a>
        @else
            <label class="badge badge-danger">{{__('messages.invalid')}} {{__('messages.customer')}} {{__('messages.data')}}</label>
        @endif
    </td>

    <td>
        @if($order->doctor)
            {{$order->doctor['name']}}
        @else
            <label class="badge badge-danger">{{__('messages.invalid')}} doctor {{__('messages.data')}}</label>
        @endif
    </td>
    
    <!-- Duration Column -->
    <td>
        @php
            $duration = is_numeric($order->duration) ? $order->duration : 0;
        @endphp
        
        @if($duration > 0)
            <span class="badge badge-info">{{ $duration }} min</span>
        @else
            @if($order->appoinmtent_type == 'offline')
                <span class="badge badge-secondary">N/A</span>
            @else
                <span class="badge badge-warning">Not set</span>
            @endif
        @endif
    </td>
    
    <!-- Consultation Fee Column -->
    <td>
        @php
            $consultationFee = is_numeric($order->consultation_fee) ? $order->consultation_fee : 0;
        @endphp
        Rs. {{ number_format($consultationFee) }}
    </td>
    
    <!-- Chat Fee Column with Calculation -->
    <td>
        @php
            $chatFee = 0;
            if ($order->doctor && isset($order->doctor->chat_fee)) {
                $chatFee = is_numeric($order->doctor->chat_fee) ? $order->doctor->chat_fee : 0;
            }
            $duration = is_numeric($order->duration) ? $order->duration : 0;
            $chatTotal = $chatFee * $duration;
        @endphp
        
        @if($chatFee > 0)
            <div>Rs. {{ number_format($chatFee) }}/min</div>
            
            @if($order->appoinmtent_type == 'chat' && $duration > 0)
                <div class="small text-muted mt-1">
                    Total: Rs. {{ number_format($chatTotal) }}
                    <br><small>({{ number_format($chatFee) }} × {{ $duration }} min)</small>
                </div>
            @endif
        @else
            —
        @endif
    </td>
    
    <!-- Audio Fee Column with Calculation -->
    <td>
        @php
            $audioFee = 0;
            if ($order->doctor && isset($order->doctor->audio_fee)) {
                $audioFee = is_numeric($order->doctor->audio_fee) ? $order->doctor->audio_fee : 0;
            }
            $duration = is_numeric($order->duration) ? $order->duration : 0;
            $audioTotal = $audioFee * $duration;
        @endphp
        
        @if($audioFee > 0)
            <div>Rs. {{ number_format($audioFee) }}/min</div>
            
            @if($order->appoinmtent_type == 'audio' && $duration > 0)
                <div class="small text-muted mt-1">
                    Total: Rs. {{ number_format($audioTotal) }}
                    <br><small>({{ number_format($audioFee) }} × {{ $duration }} min)</small>
                </div>
            @endif
        @else
            —
        @endif
    </td>
    
    <!-- Video Fee Column with Calculation -->
    <td>
        @php
            $videoFee = 0;
            if ($order->doctor && isset($order->doctor->video_fee)) {
                $videoFee = is_numeric($order->doctor->video_fee) ? $order->doctor->video_fee : 0;
            }
            $duration = is_numeric($order->duration) ? $order->duration : 0;
            $videoTotal = $videoFee * $duration;
        @endphp
        
        @if($videoFee > 0)
            <div>Rs. {{ number_format($videoFee) }}/min</div>
            
            @if($order->appoinmtent_type == 'video' && $duration > 0)
                <div class="small text-muted mt-1">
                    Total: Rs. {{ number_format($videoTotal) }}
                    <br><small>({{ number_format($videoFee) }} × {{ $duration }} min)</small>
                </div>
            @endif
        @else
            —
        @endif
    </td>
    
    <td>
        <span class="badge badge-soft-info ml-2 ml-sm-3">
            <span class="legend-indicator bg-info"></span>{{ucfirst($order['appoinmtent_type'])}}
        </span>
    </td>
    
    <!-- Calculated Total Fee Column -->
    <td>
        @php
            $calculatedFee = is_numeric($order->consultation_fee) ? $order->consultation_fee : 0;
            $appointmentType = $order->appoinmtent_type;
            
            // Calculate based on appointment type
            if (in_array($appointmentType, ['chat', 'audio', 'video'])) {
                $feeField = $appointmentType . '_fee';
                $feePerMinute = 0;
                
                if ($order->doctor && isset($order->doctor->$feeField)) {
                    $feePerMinute = is_numeric($order->doctor->$feeField) ? $order->doctor->$feeField : 0;
                }
                
                $duration = is_numeric($order->duration) ? $order->duration : 0;
                $calculatedFee = $feePerMinute * $duration;
            }
        @endphp
        
        <span class="text-primary font-weight-bold">
            Rs. {{ number_format($calculatedFee) }}
        </span>
        
        @if(in_array($appointmentType, ['chat', 'audio', 'video']) && $duration > 0)
            <div class="small text-muted">
                @if($appointmentType == 'chat' && isset($order->doctor->chat_fee))
                    ({{ number_format($order->doctor->chat_fee) }}/min × {{ $duration }} min)
                @elseif($appointmentType == 'audio' && isset($order->doctor->audio_fee))
                    ({{ number_format($order->doctor->audio_fee) }}/min × {{ $duration }} min)
                @elseif($appointmentType == 'video' && isset($order->doctor->video_fee))
                    ({{ number_format($order->doctor->video_fee) }}/min × {{ $duration }} min)
                @endif
            </div>
        @endif
    </td>
    
    <td>
        <span class="badge badge-soft-success">
            <span class="legend-indicator bg-success"></span>{{__('messages.paid')}}
        </span>
    </td>
    
    <td class="text-capitalize">
        <span class="badge badge-soft-info ml-2 ml-sm-3">
            <span class="legend-indicator bg-info"></span>{{str_replace('_',' ',$order['status'])}}
        </span>
    </td>
    
    <td>
        <a class="btn btn-sm btn-white" href="javascript:void(0);"
           onclick="deleteAppointment(event, {{ $order->id }})"
           title="{{ __('messages.delete') }}">
           <i class="tio-delete-outlined"></i>
        </a>

        <form id="delete-form-{{ $order->id }}" 
              action="{{ route('doctor.appointment.destroy', $order->id) }}" 
              method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </td>
</tr>
@endforeach

<script>
function deleteAppointment(event, id) {
    event.preventDefault();
    let confirmation = confirm('Are you sure you want to delete this appointment?');
    if (confirmation) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>