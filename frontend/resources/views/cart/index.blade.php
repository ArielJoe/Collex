@extends('components.layout')

@section('title')
    Keranjang Belanja Anda
@endsection

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom spinner for buttons */
        .btn-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s linear infinite;
            margin-right: 0.5rem;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .cart-item-card {
            transition: all 0.3s ease;
        }

        .cart-item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
        }
    </style>
@endpush

@section('content')
    @include('components.navbar')

    <div class="bg-gray-100 min-h-screen py-8 md:py-12">
        <div class="container mx-auto px-4 max-w-4xl">

            {{-- Display Success/Error Messages from Session --}}
            @if (session('success'))
                <div
                    class="mb-6 p-4 bg-green-100 text-green-700 border border-green-300 rounded-md shadow-sm flex justify-between items-center">
                    <span>{{ session('success') }}</span>
                    <button type="button" onclick="this.parentElement.style.display='none'"
                        class="text-green-700 hover:text-green-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif
            @if (session('error'))
                <div
                    class="mb-6 p-4 bg-red-100 text-red-700 border border-red-300 rounded-md shadow-sm flex justify-between items-center">
                    <span>{{ session('error') }}</span>
                    <button type="button" onclick="this.parentElement.style.display='none'"
                        class="text-red-700 hover:text-red-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif
            @if (isset($error) && $error)
                {{-- Error dari controller saat fetch data --}}
                <div
                    class="mb-6 p-4 bg-red-100 text-red-700 border border-red-300 rounded-md shadow-sm flex justify-between items-center">
                    <span>{{ $error }}</span>
                    <button type="button" onclick="this.parentElement.style.display='none'"
                        class="text-red-700 hover:text-red-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif


            <div class="bg-white rounded-xl shadow-lg p-6 md:p-8">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-8 border-b pb-4">
                    <i class="fas fa-shopping-cart text-red-500 mr-3"></i>Keranjang Belanja Anda
                </h1>

                @if (isset($cartData) && count($cartData) > 0)
                    <div id="cartItemsContainer" class="space-y-6">
                        @php $subtotal = 0; @endphp
                        @foreach ($cartData as $item)
                            @php
                                $itemName = 'Nama Item Tidak Tersedia';
                                $itemPrice = 0;
                                $itemImage = 'https://via.placeholder.com/150?text=No+Image';
                                $eventName = $item['event_id']['name'] ?? 'Nama Event Tidak Diketahui';
                                $eventDate = isset($item['event_id']['start_time'])
                                    ? \Carbon\Carbon::parse($item['event_id']['start_time'])->isoFormat('D MMMM YYYY')
                                    : 'Tanggal Tidak Diketahui';

                                if (isset($item['detail_id']) && $item['detail_id'] !== null) {
                                    $itemName = $item['detail_id']['title'] ?? 'Sesi Event';
                                    $itemPrice = floatval(
                                        $item['detail_id']['price']['$numberDecimal'] ??
                                            ($item['detail_id']['price'] ?? 0),
                                    );
                                    if (isset($item['event_id']['poster_url'])) {
                                        $itemImage = $item['event_id']['poster_url'];
                                    }
                                } elseif (isset($item['package_id']) && $item['package_id'] !== null) {
                                    $itemName = $item['package_id']['package_name'] ?? 'Paket Event';
                                    $itemPrice = floatval(
                                        $item['package_id']['price']['$numberDecimal'] ??
                                            ($item['package_id']['price'] ?? 0),
                                    );
                                    if (isset($item['event_id']['poster_url'])) {
                                        $itemImage = $item['event_id']['poster_url'];
                                    }
                                }
                                $subtotal += $itemPrice;
                            @endphp
                            <div
                                class="cart-item-card bg-white rounded-lg shadow-md border border-gray-200 p-4 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                <img src="{{ $itemImage }}" alt="{{ $itemName }}"
                                    class="w-full sm:w-24 h-32 sm:h-24 object-cover rounded-md flex-shrink-0">
                                <div class="flex-grow">
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $itemName }}</h3>
                                    <p class="text-sm text-gray-500">Event: {{ $eventName }}</p>
                                    <p class="text-sm text-gray-500">Tanggal Event: {{ $eventDate }}</p>
                                </div>
                                <div class="flex flex-col items-start sm:items-end sm:ml-auto mt-4 sm:mt-0">
                                    <p class="text-lg font-semibold text-red-500 mb-2">Rp
                                        {{ number_format($itemPrice, 0, ',', '.') }}</p>
                                    <form action="{{ route('cart.item.remove', $item['_id']) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus item ini?');">
                                        @csrf
                                        {{-- @method('DELETE') --}} {{-- Uncomment jika rute Anda menggunakan DELETE --}}
                                        <button type="submit"
                                            class="text-xs text-gray-500 hover:text-red-600 transition duration-150">
                                            <i class="fas fa-trash-alt mr-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Cart Summary --}}
                    <div id="cartSummary" class="mt-8 border-t pt-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold text-gray-800">Ringkasan Belanja</h2>
                            <form action="{{ route('cart.clear') }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin mengosongkan seluruh keranjang?');">
                                @csrf
                                {{-- @method('DELETE') --}}
                                <button type="submit"
                                    class="text-red-500 hover:text-red-700 text-sm font-medium transition duration-300">
                                    <i class="fas fa-trash-alt mr-1"></i> Kosongkan Keranjang
                                </button>
                            </form>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg space-y-3">
                            <div class="flex justify-between text-gray-700">
                                <span>Subtotal (<span id="cartItemCount">{{ count($cartData) }}</span> item)</span>
                                <span id="cartSubtotal" class="font-semibold">Rp
                                    {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-gray-900 font-bold text-xl border-t pt-3 mt-3">
                                <span>Total Pembayaran</span>
                                <span id="cartTotal">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="mt-8 text-right">
                            <a href="{{ route('checkout.page') }}" {{-- Mengarahkan ke halaman checkout --}}
                                class="bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-6 rounded-lg text-md transition duration-300 shadow-md hover:shadow-lg">
                                Lanjut ke Pembayaran
                            </a>
                        </div>
                    </div>
                @elseif(!isset($error))
                    {{-- Tampilkan pesan kosong jika tidak ada error dan tidak ada data --}}
                    <div class="text-center py-10" id="cartEmpty">
                        <i class="fas fa-shopping-bag text-4xl text-gray-400 mb-4"></i>
                        <p class="text-xl text-gray-700 font-semibold mb-2">Keranjang Anda kosong</p>
                        <p class="text-gray-500 mb-6">Sepertinya Anda belum menambahkan event apapun ke keranjang.</p>
                        <a href="{{ url('/') }}"
                            class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-6 rounded-lg transition duration-300">
                            Cari Event
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
