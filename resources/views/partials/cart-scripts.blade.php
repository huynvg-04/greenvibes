<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    window.CartConfig = {
        csrfToken: '{{ csrf_token() }}',

        routes: {
            update: "{{ route('user.cart.update') }}",
            remove: "{{ route('user.cart.remove') }}",
            select: "{{ route('user.cart.select') }}",
            checkout: "{{ route('user.checkout.index') }}"
        },

        couponDiscount: {{ session()->has('coupon') ? session('coupon')['discount_amount'] : 0 }}
    };
</script>

<script src="{{ asset('js/cart.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    window.CartConfig = {
        csrfToken: '{{ csrf_token() }}',

        routes: {
            update: "{{ route('user.cart.update') }}",
            remove: "{{ route('user.cart.remove') }}",
            select: "{{ route('user.cart.select') }}",
            checkout: "{{ route('user.checkout.index') }}"
        },

        couponDiscount: {{ session()->has('coupon') ? session('coupon')['discount_amount'] : 0 }}
    };
</script>

<script src="{{ asset('js/cart.js') }}"></script>