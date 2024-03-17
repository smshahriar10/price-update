@extends('layouts.main')

@section('title', 'Product Price Update')

@push('styles')

@endpush

@section('content')
    <div class="col-12 col-md-2 col-lg-2"></div>
    <div class="col-12 col-md-8 col-lg-8">
        <div class="login-brand">Product Price Updates</div>
    </div>
    <div class="col-12 col-md-2 col-lg-2"></div>
    <div class="col-12 col-md-2 col-lg-2"></div>
    <div class="col-12 col-md-8 col-lg-8">
        <div id="updatingPricesNotification" style="display: none;" class="alert alert-info" role="alert">
            The product prices are updating. Please wait...
        </div>
        <form action="{{ route('price.update') }}" method="POST">
            @sessionToken
            <div class="card">
                <div class="card-header">
                    <h4>Exchange Rate</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>USD to BRL</label> <i class="material-icons"  data-toggle="tooltip" title="" data-original-title="Prices auto-update every 24 hours or can be manually updated as needed.">help</i>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                Current rate
                                </div>
                            </div>
                            <input type="number" class="form-control currency" name="oldrate" value="{{ $newRate }}" placeholder="Not set" step="0.00001" min="1" readonly>

                            <div class="input-group-prepend pl-2">
                                <div class="input-group-text border-radius-left">
                                New rate
                                </div>
                            </div>
                            <input type="number" class="form-control currency" name="newrate" value="" placeholder="Add new rate" step="0.00001" min="1" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update price now</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-12 col-md-2 col-lg-2"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const submitButton = form.querySelector('button[type="submit"]'); // Assuming there's only one submit button in your form

    form.addEventListener('submit', function () {
        // Display the updating prices message
        document.getElementById('updatingPricesNotification').style.display = 'block';
        
        // Disable the submit button to prevent multiple submissions
        submitButton.disabled = true;
        submitButton.innerText = 'Updating...'; // Optional: Change button text to indicate the process
    });
});
</script>
@endpush