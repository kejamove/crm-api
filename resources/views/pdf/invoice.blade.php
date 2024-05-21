<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tailwind Page</title>
    <!-- Removed Tailwind CSS via CDN -->
    <style>
        body {
            background-color: #f7fafc; /* bg-gray-100 */
            padding: 0.5rem; /* p-2 */
            height: 100vh; /* h-screen */
            width: 100%; /* w-full */
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            margin: auto;
            padding: 1rem; /* p-4 */
            background-color: #ffffff; /* bg-white */
            height: 100%; /* h-full */
            border-radius: 0.375rem; /* rounded-md */
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem; /* gap-4 */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 50%; /* md:w-6/12 */
        }
        .container img {
            width: auto; /* w-fit */
            max-width: 100%;
        }
        .container h1 {
            font-size: 2.25rem; /* text-4xl */
            font-weight: 700; /* font-bold */
            margin-bottom: 1rem; /* mb-4 */
            color: #2d3748; /* text-gray-800 */
        }
        .container p {
            font-size: 1.125rem; /* text-lg */
            text-align: center;
            max-width: 75%; /* md:w-9/12 */
            margin: 0 auto;
        }
        .info-box {
            min-width: 75%;
            background-color: #f7fafc;
            border: 2px solid #e2e8f0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            height: fit-content;
        }
        .info-box > div {
            padding: 0 8px 8px 8px;
            display: flex;
            flex-direction: column;
            gap: .5rem;
            width: 100%;
            text-align: start;
        }

        .info-box > div > * {
            width: 100%;
        }

        .info-box > div:last-child {
            border-right: none;
            border-left: 2px solid #e2e8f0;

        }
    </style>
</head>
<body>
{{--{{$data}}--}}
<div class="container">
    <img src="{{ asset('kejamove_logo.png') }}" alt="KEJA MOVE LOGO">

    <h1>Your Moving Quotation</h1>

    <p>Thank you for inviting Keja Move Ltd.
        Our quotations are based on the distance between origin and
        destination locations as well as volume of your inventory.
    </p>

    <div class="info-box">
        <div>
            <h3>MOVING FROM</h3>
            <div>source</div>
        </div>

        <div  >
            <h3>MOVING TO</h3>
            <div>destination</div>
        </div>
    </div>
</div>
</body>
</html>
