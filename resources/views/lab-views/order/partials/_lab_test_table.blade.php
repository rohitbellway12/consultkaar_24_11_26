                    @foreach($orders as $key=>$order)

                        <tr class="status-{{$order['order_status']}} class-all">
                            <td class="">
                                {{$key + 1}}
                            </td>
                            <td class="table-column-pl-0">
                            {{$order['booking_id']}}

                      
                            </td>
                            <td>{{date('d M Y',strtotime($order['created_at']))}}</td>
                            <td>
                                @if($order->customer)
                                 {{$order->customer['name'] ?? 'N/A'}}

                                  
                                @else
                                    <label class="badge badge-danger">{{__('messages.invalid')}} {{__('messages.customer')}} {{__('messages.data')}}</label>
                                @endif
                            </td>
                            <td>
                                @if($order->customer)
                                 {{$order->customer['mobile_number']}}

                                  
                                @else
                                    <label class="badge badge-danger">{{__('messages.invalid')}} {{__('messages.customer')}} {{__('messages.data')}}</label>
                                @endif
                            </td>
                        
                        
                            <td>Rs. {{ number_format($order->amount)  }} </td>
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
                                        @if($order['status']!='reject')
                                            <a class="btn btn-sm btn-white" onclick="form_alert('lab-test-{{$order->id}}','Want to reject this booking ?')"
                                               href="javascript:">
                                               <i class="tio-cross"></i> Reject
                                            </a>
    
    
                                            <form action="{{ url('lab/manage-lab-test-booking/' . $order->id . '/reject') }}" 
                                                    method="post" id="lab-test-{{$order->id}}">
                                                @csrf @method('post')
                                            </form>
                                            
                                        @endif

                                        
                            </td>
                        </tr>

                    @endforeach
