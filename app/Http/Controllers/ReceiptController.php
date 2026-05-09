<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Receipt;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\OcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceiptController extends Controller
{
    public function __construct(private OcrService $ocr) {}

    public function showUpload()
    {
        return view('receipts.upload');
    }

    public function process(Request $request)
    {
        $request->validate([
            'receipt_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'receipt_image.required' => 'Pilih foto struk terlebih dahulu.',
            'receipt_image.image'    => 'File harus berupa gambar.',
            'receipt_image.max'      => 'Ukuran foto maksimal 5MB.',
        ]);

        $path = $request->file('receipt_image')
            ->store('receipts/' . Auth::id(), 'public');

        $receipt = Receipt::create([
            'user_id'    => Auth::id(),
            'image_path' => $path,
            'status'     => 'pending',
        ]);

        // Scan via Gemini
        $result = $this->ocr->scanReceipt($path);

        $receipt->update([
            'store_name'   => $result['store_name'] ?? null,
            'receipt_date' => $result['receipt_date'] ?? now()->toDateString(),
            'raw_text'     => json_encode($result),
            'status'       => 'processed',
        ]);

        // Simpan ke session
        session(['scan_result_' . $receipt->id => $result]);

        return redirect()->route('receipt.result', $receipt);
    }

    public function showResult(Receipt $receipt)
    {
        abort_if($receipt->user_id !== Auth::id(), 403);

        // Ambil dari session, fallback ke raw_text
        $result = session('scan_result_' . $receipt->id);

        if (empty($result)) {
            $result = json_decode($receipt->raw_text, true) ?? [];
        }

        $categories = Category::all();

        return view('receipts.show', compact('receipt', 'result', 'categories'));
    }

    public function save(Request $request, Receipt $receipt)
    {
        abort_if($receipt->user_id !== Auth::id(), 403);

        $request->validate([
            'category_id'        => 'required|exists:categories,id',
            'transaction_date'   => 'required|date',
            'total_amount'       => 'required|numeric|min:0',
            'notes'              => 'nullable|string|max:255',
            'items'              => 'nullable|array',
            'items.*.item_name'  => 'required|string',
            'items.*.qty'        => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.subtotal'   => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $receipt) {
            $transaction = Transaction::create([
                'user_id'          => Auth::id(),
                'receipt_id'       => $receipt->id,
                'category_id'      => $request->category_id,
                'total_amount'     => $request->total_amount,
                'notes'            => $request->notes,
                'transaction_date' => $request->transaction_date,
            ]);

            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'item_name'      => $item['item_name'],
                        'qty'            => $item['qty'],
                        'unit_price'     => $item['unit_price'],
                        'subtotal'       => $item['subtotal'],
                    ]);
                }
            }
        });

        return redirect()->route('dashboard')
            ->with('success', 'Struk berhasil disimpan! 🎉');
    }
}