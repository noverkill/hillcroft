<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <section>
        <h1>Upload Product XML File</h1>
        <form action="{{ route('products.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div>
                <label for="xml_file">XML product file:</label>
                <input type="file" name="xml_file">
            </div>
            <div style="margin-top:10px">
                <button type="submit">Upload</button>
            </div>
        </form>
        @if (session('success'))
            <div style="color: green;">
                {!! nl2br(e(session('success'))) !!}
            </div>
        @endif

        @if (session('error'))
            <div style="color: red;">
                {!! nl2br(e(session('error'))) !!}
            </div>
        @endif
    </section>
    <section>
    <h1>List of Products</h1>
    <table>
        <tr>
            <th>Code</th>
            <th>Category</th>
            <th>Name</th>
            <th>Price (Ex. VAT)</th>
            <th>Price (Inc. VAT)</th>
            <th>Stock</th>
            <th>Description</th>
        </tr>
        @foreach ($products as $product)
            <tr>
                <td>{{ $product->code }}</td>
                <td>{{ $product->cat }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->price_ex_vat }}</td>
                <td>{{ $product->price_inc_vat }}</td>
                <td>{{ $product->stock }}</td>
                <td>{{ $product->short_desc }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>