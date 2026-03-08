      <table class="table table-bordered">
                                        <thead class="bg-body">
                                              <tr>
                                 <th>#</th>
                                                        

                                       

                                            <th> Name </th>
                                        
                                            <th> Email </th>
                                            <th> Phone(Work) </th>
                                            <th> Mobile </th>
                                                   <th> Access Type </th>
                           
                                                <th> Portal Access </th>
                                           
                                            
                                        </tr>
                                        </thead>
                                      <tbody id="showdata">
                                          @php  $sno=0; @endphp
                                        @foreach($qry as $q)
                                        <tr>
                                             <td>{{++$sno}}</td>
                                            
                                            <td class="font-w600">
                                                 {{$q->firstname}}{{$q->lastname}} 
                                            </td>
                                       
                                            <td>{{$q->email}}</td>
                                               <td>{{$q->work_phone}}</td>
                                                    <td>{{$q->mobile}}</td>
                                                      <td>{{$q->role}}</td>
                                               <td>
                                                @if($q->portal_access==1)
                                                   Yes 
                                                    @else
                                               No 
                                                    @endif
                                            </td>
 
                                         
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    </table>