      <table class="table table-bordered">
                                        <thead class="bg-body">
                                              <tr>
                                 <th>#</th>
                                       <th> Site </th>
                                        
                                            <th> Client </th>
                                          
                                            
                                        </tr>
                                        </thead>
                                      <tbody id="showdata">
                                          @php  $sno=0; @endphp
                                        @foreach($qry as $q)
                                        <tr>
                                             <td>{{++$sno}}</td>
                                            
                                            <td class="font-w600">
                                                 {{$q->domain_name}}  
                                            </td>
                                       
                                            <td>{{$q->client_display_name}}</td>
                                              
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    </table>