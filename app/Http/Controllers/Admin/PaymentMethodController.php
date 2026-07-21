<?php

namespace App\Http\Controllers\Admin;

use App\Models\PaymentMethod;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ImageUploadService;

class PaymentMethodController extends Controller
{
    public function __construct(protected ImageUploadService $imageService) {}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', PaymentMethod::class);
        $methods = PaymentMethod::orderBy('sort_order')->get();
        return view('admin.settings.payment.index', compact('methods'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        $this->authorize('create', PaymentMethod::class);
        return view('admin.settings.payment.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', PaymentMethod::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:payment_methods,code',
            'sort_order' => 'integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $this->imageService->upload(
                $request->file('image'),
                'payment_methods',
                45   // nhỏ vì icon thanh toán
            );
        }

        PaymentMethod::create($data);

        return redirect()->route('admin.settings.payment.index')->with('success', 'Thêm mới phương thức thanh toán.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit(PaymentMethod $payment)
    {
        $this->authorize('update', $payment);
        return view('admin.settings.payment.edit', compact('payment'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentMethod $payment)
    {
        $this->authorize('update', $payment);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:payment_methods,code,' . $payment->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $this->imageService->replace(
                $request->file('image'),
                $payment->image,
                'payment_methods',
                45
            );
        }

        $payment->update($data);

        return redirect()->route('admin.settings.payment.index')->with('success', 'Cập nhật phương thức thanh toán.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentMethod $payment)
    {
        $this->authorize('delete', $payment);

        $this->imageService->delete($payment->image);

        $payment->delete();
        return back()->with('success', 'Xóa phương thức thanh toán.');
    }
}
