<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment for Booking #:id', ['id' => $booking->id]) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Payment Details -->
                    <div class="prose">
                        <h3 class="text-xl font-bold">Payment Instructions</h3>
                        <p>Please complete your payment before the deadline. Your booking will be automatically cancelled if payment is not received in time.</p>

                        <div class="mt-4">
                            <p class="text-gray-600">Total Amount:</p>
                            <p class="text-3xl font-bold text-indigo-600">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                        </div>

                        @if ($booking->transaction && $booking->transaction->payment_due_at)
                        <div class="mt-4" x-data="countdown('{{ $booking->transaction->payment_due_at->toIso8601String() }}')" x-init="init()">
                            <p class="text-gray-600">Payment Deadline:</p>
                            <p class="font-semibold text-red-600" x-show="!expired">
                                <span x-text="time.days"></span>d :
                                <span x-text="time.hours"></span>h :
                                <span x-text="time.minutes"></span>m :
                                <span x-text="time.seconds"></span>s
                            </p>
                            <p class="font-semibold text-red-600" x-show="expired">
                                Time has expired!
                            </p>
                        </div>
                        @endif

                        <ul class="mt-4 list-disc list-inside text-gray-700">
                            <li>Scan the QR code using your mobile banking or e-wallet application.</li>
                            <li>Ensure the amount matches exactly.</li>
                            <li>Payment will be verified manually by our staff.</li>
                        </ul>
                    </div>

                    <!-- QR Code -->
                    <div class="text-center">
                        <h4 class="font-semibold">Scan QRIS to Pay</h4>
                        {{-- Ganti 'images/qris.png' dengan path ke QR code Anda --}}
                        <img src="{{ asset('images/qris.png') }}" alt="QRIS Payment Code" class="mx-auto mt-2 border-4 border-gray-200 rounded-lg w-64 h-64">
                        <a href="{{ route('bookings.index') }}" class="mt-6 inline-block bg-gray-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-gray-700">
                            Back to My Bookings
                        </a>
                    </div>
                    <div class="mt-8 border-t pt-6">
                        @if ($booking->transaction->status == 'pending')
                        <h3 class="text-lg font-bold">Upload Payment Proof</h3>
                        <p class="text-sm text-gray-600 mb-4">After making the payment, please upload a screenshot of your transfer receipt here.</p>

                        <form action="{{ route('payment.upload', $booking) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div>
                                <x-input-label for="payment_proof" :value="__('Receipt File (Image only, max 2MB)')" />
                                <x-text-input id="payment_proof" name="payment_proof" type="file" class="mt-1 block w-full" required />
                                <x-input-error :messages="$errors->get('payment_proof')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-primary-button>
                                    {{ __('Submit Proof of Payment') }}
                                </x-primary-button>
                            </div>
                        </form>
                        @elseif($booking->transaction->status == 'waiting_confirmation')
                        <div class="text-center p-4 bg-blue-100 text-blue-800 rounded-lg">
                            <p class="font-semibold">Your payment proof has been uploaded and is waiting for confirmation from our staff.</p>
                        </div>
                        @elseif($booking->transaction->status == 'paid')
                        <div class="text-center p-4 bg-green-100 text-green-800 rounded-lg">
                            <p class="font-semibold">Your payment has been confirmed. Thank you!</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function countdown(expiry) {
            return {
                expiry: new Date(expiry),
                expired: false,
                time: {
                    days: '00',
                    hours: '00',
                    minutes: '00',
                    seconds: '00',
                },
                init() {
                    this.update();
                    const interval = setInterval(() => {
                        this.update();
                        if (this.expired) {
                            clearInterval(interval);
                        }
                    }, 1000);
                },
                update() {
                    const now = new Date();
                    const diff = this.expiry.getTime() - now.getTime();

                    if (diff <= 0) {
                        this.expired = true;
                        return;
                    }

                    this.time.days = this.pad(Math.floor(diff / (1000 * 60 * 60 * 24)));
                    this.time.hours = this.pad(Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)));
                    this.time.minutes = this.pad(Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60)));
                    this.time.seconds = this.pad(Math.floor((diff % (1000 * 60)) / 1000));
                },
                pad(num) {
                    return num.toString().padStart(2, '0');
                }
            }
        }
    </script>
</x-app-layout>