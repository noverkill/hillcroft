<!DOCTYPE html>
<html>
<head>
    <title>Products imported with No Stock</title>
</head>
<body>
    <h4>Products imported with No Stock</h4>
    @if(isset($products))
        @if (count($products) > 0)
            <ul>
                @foreach ($products as $product)
                    <li>{{ $product->name }}</li>
                @endforeach
            </ul>
        @endif
    @endif
</body>
</html>