<?php

namespace App\Http\Controllers;

use App\Models\Product;
// use Faker\Core\File;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(){
        $products = Product::orderBy('created_at','DESC')->get();
return view('products.list',[
    'products' => $products
]);

    }

    // =========================end index============

    public function create(){
        return view('products.create');

    }
        // =========================end create============

    public function store(Request $request){
$rules =[
    'name'=> 'required|min:5',
    'sku'=> 'required|min:3',
    'price'=> 'required|numeric',


];

if($request->image != ""){
$rules['image']='image';
}
        $validator=Validator::make($request->all(),$rules);
if($validator->fails()){
    return redirect()->route('products.create')->withInput()->withErrors($validator);
}

$product =new Product();
$product -> name = $request->name;
$product -> sku = $request->sku;
$product -> price = $request->price;
$product -> description = $request->description;
$product->save();

// here will store image
if($request->image != ""){
$image = $request->image;
$ext = $image->getClientOriginalExtension();
$imageName = time().'.'.$ext;//uniqye image name
$image->move(public_path('uploads/products'),$imageName);
// save the image databases 
$product->image = $imageName;
$product->save();
}
return redirect()->route('products.index')->with('success','Product added succesfully');


    }
        // =========================end store============

    public function edit($id){

        $product = Product::findOrFail($id);

        return view('products.edit',[
            'product' => $product
        ]);
    }
        // =========================end edit============

    public function update($id,Request $request){

        $product = Product::findOrFail($id);


        $rules =[
            'name'=> 'required|min:5',
            'sku'=> 'required|min:3',
            'price'=> 'required|numeric',
        
        
        ];
        
        if($request->image != ""){
        $rules['image']='image';
        }
                $validator=Validator::make($request->all(),$rules);
        if($validator->fails()){
            return redirect()->route('products.edit',$product->id)->withInput()->withErrors($validator);
        }
        
        // $product =new Product();
        $product -> name = $request->name;
        $product -> sku = $request->sku;
        $product -> price = $request->price;
        $product -> description = $request->description;
        $product->save();
        
        // here will store image
        if($request->image != ""){
            // delete old images
        File::delete(public_path('uploads/products/'.$product->image));
            
        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $imageName = time().'.'.$ext;//uniqye image name
        $image->move(public_path('uploads/products'),$imageName);
        // update the image databases 
        $product->image = $imageName;
        $product->save();
        }
        return redirect()->route('products.index')->with('success','Product added succesfully');
        
    }
        // =========================end update============

    public function destroy($id){
        $product = Product::findOrFail($id);

        File::delete(public_path('uploads/products/'.$product->image));
        // delete from databases
        $product->delete();
        
        return redirect()->route('products.index')->with('success','Product deleted succesfully');

    }
        // =========================end destroy============

}
