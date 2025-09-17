<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Stripe Payment Test</title>

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://js.stripe.com/v3/"></script>

<style>
    body { background-color: #f8f9fa; }
    .card { border-radius: 15px; }
    #card-element { padding: 15px; border: 1px solid #ced4da; border-radius: 5px; }
</style>
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header text-center bg-primary text-white rounded-top-4">
                    <h4 class="mb-0">💳 Stripe Payment (Test Mode)</h4>
                </div>
                <div class="card-body p-4">

                    <!-- Success Message -->
                    @if (session('success'))
                        <div class="alert alert-success text-center">{{ session('success') }}</div>
                    @endif

                    <!-- Error Message -->
                    @if (session('error'))
                        <div class="alert alert-danger text-center">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('stripe.store') }}" method="POST" id="payment-form">
                        @csrf

                        <!-- Dummy Name Input -->
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                        </div>

                        <!-- Dummy Email Input -->
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                        </div>

                        <!-- Amount -->
                        <div class="mb-3">
                            <label class="form-label">Amount (USD)</label>
                            <input type="number" name="amount" class="form-control" value="100" min="1" required>
                        </div>

                        <!-- Stripe Card Element (hide postal code for dummy test) -->
                        <div class="mb-3">
                            <label class="form-label">Card Details</label>
                            <div id="card-element" class="form-control p-3"></div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">Pay $100</button>
                    </form>
                </div>
                <div class="card-footer text-center small text-muted">
                    Stripe Test Payment 🔒 Use card number: 4242 4242 4242 4242
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const stripe = Stripe("{{ env('STRIPE_KEY') }}");
    const elements = stripe.elements();

    // Hide postal code to avoid invalid postal code errors in test
    const card = elements.create('card', { hidePostalCode: true });
    card.mount('#card-element');

    const form = document.getElementById('payment-form');
    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const {token, error} = await stripe.createToken(card);
        if (error) {
            alert(error.message);
        } else {
            // Append token to form and submit
            const hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);
            form.submit();
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
