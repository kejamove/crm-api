<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Email Template</title>
    <style>
        /* Inline CSS for compatibility */
        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <h1 class="text-blue-500" style="color:blue;"> Hellooooooo </h1>
    <table class="container mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
        <tr>
            <td class="px-6 py-8">
                <div class="text-center">
                    <h1 class="text-3xl font-semibold text-gray-800">Hello, {{ $to_name }}</h1>
                    <h1 class="text-3xl font-semibold text-gray-800"> {{ $phone_number }}</h1>
                    <p class="mt-2 text-lg text-gray-600">{{ $client_message }}</p>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
