                    @foreach($orders as $key=>$order)

                        <tr class="status-{{$order['order_status']}} class-all">
                            <td class="">
                                {{$key + 1}}
                            </td>
                            <td class="table-column-pl-0">
                             {{$order['id']}}

                      
                            </td>
                            <td>{{date('d M Y',strtotime($order['appoinmtent_date']))}}</td>
                            <td>{{$order['patent_name']}}  </td>



                            <td>
                                @if($order->doctor)
                                 {{$order->doctor['name']}}

                                  
                                @else
                                    <label class="badge badge-danger">{{__('messages.invalid')}} doctor {{__('messages.data')}}</label>
                                @endif
                            </td>
                        
                        
                            <td>Rs. {{ number_format($order->consultation_fee)  }} </td>
                            <td>
                              
                                <span class="badge badge-soft-info ml-2 ml-sm-3">
                                    <span class="legend-indicator bg-info"></span>{{ucfirst($order['appoinmtent_type'])}}
                                  </span>
                            
                        </td>
                            <td>
                              
                                <span class="badge badge-soft-success">
                                  <span class="legend-indicator bg-success"></span>{{__('messages.paid')}}
                                </span>
                            
                        </td>
                        <td>
                              
                            <span class="badge badge-soft-info ml-2 ml-sm-3">
                                <span class="legend-indicator bg-info"></span>{{ucfirst($order['status'])}}
                              </span>
                        
                    </td>
                           
                        </tr>

                    @endforeach
