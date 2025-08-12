@extends('client.layouts.guest')

@section('content')
   <div class="text-center">
       <div class="container mt-5">
           <div class="row justify-content-center">
               <div class="col-md-8 col-lg-6">
                   <div class="card shadow-lg border-0">
                       <div class="card-body p-5">
                           <div class="mb-4">
                               <i class="ki-filled ki-message-question text-warning" style="font-size: 4rem;"></i>
                           </div>
                           
                           <h2 class="text- mb-4">Branch Not Found</h2>
                           
                           <div class="alert alert-warning" role="alert">
                               <h5 class="alert-heading">
                                   <i class="fas fa-info-circle me-2"></i>
                                   Sorry, we couldn't find your visited branch
                               </h5>
                               <hr>
                               <p class="mb-0">
                                   Please scan the QR code again to access the correct branch information.
                               </p>
                           </div>
                           
                           <div class="mt-4 p-4 bg-light rounded">
                               <h6 class="text-primary mb-3">
                                   <i class="fas fa-question-circle me-2"></i>
                                   Need Help?
                               </h6>
                               <p class="mb-2">
                                   For further information and assistance, please contact our front desk.
                               </p>
                               <small class="text-muted">
                                   Our staff will be happy to help you with any questions or concerns.
                               </small>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>
@endsection