<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    /**
     * Generate and return a downloadable invoice PDF
     *
     * @param int $tagihanId
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateInvoice($tagihanId)
    {
        try {
            // Check if the tagihan exists and is paid
            $tagihan = Tagihan::findOrFail($tagihanId);
            
            if ($tagihan->tagihanStatus !== 'Lunas') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tagihan belum lunas'
                ], 400);
            }
            
            // Generate the PDF
            $pdf = $this->generatePdf($tagihanId);
            
            // Save the PDF to storage
            $filename = 'invoice-' . $tagihan->tagihanKode . '-' . Str::random(8) . '.pdf';
            $path = 'invoices/' . $filename;
            
            Storage::disk('public')->put($path, $pdf->output());
            
            // Get the full URL to the PDF
            $url = url('storage/' . $path);
            
            return response()->json([
                'success' => true,
                'invoice_url' => $url,
                'filename' => $filename
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat invoice: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Preview the invoice in HTML format
     *
     * @param int $tagihanId
     * @return \Illuminate\Http\Response
     */
    public function previewInvoice($tagihanId)
    {
        try {
            // Check if the tagihan exists and is paid
            $tagihan = Tagihan::findOrFail($tagihanId);
            
            if ($tagihan->tagihanStatus !== 'Lunas') {
                return response()->view('errors.invoice', [
                    'message' => 'Tagihan belum lunas'
                ], 400);
            }
            
            // Get the data for the invoice
            $data = $this->prepareInvoiceData($tagihanId);
            
            // Return the HTML view directly
            return view('transaksis.struk.index2', compact('data'));
        } catch (\Exception $e) {
            return response()->view('errors.invoice', [
                'message' => 'Gagal membuat preview invoice: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate the PDF for an invoice
     *
     * @param int $tagihanId
     * @return \Barryvdh\DomPDF\PDF
     */
    private function generatePdf($tagihanId)
    {
        $data = $this->prepareInvoiceData($tagihanId);
        
        $pdf = Pdf::loadView('transaksis.struk.index', compact('data'))
            ->setPaper([0, 0, 306, 1181], 'portrait')
            ->set_option('isHtml5ParserEnabled', true)
            ->set_option('isPhpEnabled', true)
            ->set_option('defaultFont', 'Tahomaku');
            
        return $pdf;
    }
    
    /**
     * Prepare data for the invoice
     *
     * @param int $tagihanId
     * @return array
     */
    private function prepareInvoiceData($tagihanId)
    {
        $tagihan = Tagihan::findOrFail($tagihanId);
        $pembayaran = Pembayaran::where('pembayaranTagihanId', $tagihanId)->firstOrFail();

        return [
            'tagihanKode' => $tagihan->tagihanKode,
            'pelangganKode' => $tagihan->pelanggan->pelangganKode,
            'pelangganNama' => $tagihan->pelanggan->pelangganNama,
            'tagihanMeteranAwal' => $tagihan->tagihanMAwal,
            'tagihanMeteranAkhir' => $tagihan->tagihanMAkhir,
            'nama_bulan' => $tagihan->bulan->bulanNama,
            'tagihanTahun' => $tagihan->tagihanTahun,
            'formattedTagihanTotal' => number_format($pembayaran->pembayaranJumlah, 0, ',', '.'),
            'formattedTotalDenda' => number_format($pembayaran->pembayaranAbonemen, 0, ',', '.'),
            'pembayaranKasirName' => User::find($pembayaran->pembayaranKasirId)->name ?? 'Aplikasi',
            'formattedTotal' => number_format($pembayaran->pembayaranJumlah + $pembayaran->pembayaranAbonemen, 0, ',', '.'),
            'date' => $tagihan->tagihanDibayarPadaWaktu,
            'name' => "Kasir",
        ];
    }
    
    /**
     * Download the invoice directly (for web usage)
     *
     * @param int $tagihanId
     * @return \Illuminate\Http\Response
     */
    public function downloadInvoice($tagihanId)
    {
        try {
            $tagihan = Tagihan::findOrFail($tagihanId);
            $pembayaran = Pembayaran::where('pembayaranTagihanId', $tagihanId)->firstOrFail();
            
            $pdf = $this->generatePdf($tagihanId);
            
            return $pdf->stream('struk-pembayaran-' . $tagihan->pelanggan->pelangganKode . '-' . $tagihan->tagihanKode . '.pdf');
        } catch (\Exception $e) {
            return response()->view('errors.invoice', [
                'message' => 'Gagal mengunduh invoice: ' . $e->getMessage()
            ], 500);
        }
    }
}
