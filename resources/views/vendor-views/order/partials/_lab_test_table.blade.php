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
                                 <a class="text-body text-capitalize"
                                       href="{{route('admin.customer.view',[$order['user_id']])}}">{{$order->customer['name']}}</a> 

                                  
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
                                <a class="btn btn-sm btn-white"
                                href="{{route('admin.booking-detail',['id'=>$order['id']])}}"><i
                                                class="tio-visible"></i> {{__('messages.view')}}</a>

                              @if($order['status']=='cancelled' && $order['refunded'] == NULL)
                              <a class="btn btn-sm btn-white ml-2 refund-money"
                              data-order-id="{{ $order['id'] }}"><i
                                   class="tio-visible"></i> Refund
                               </a>

                              @endif
                            </td>
                        </tr>

                    @endforeach
