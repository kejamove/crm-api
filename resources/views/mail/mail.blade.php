<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <h1 class="text-blue-500" style="color:blue;"> KEJA MOVE </h1>
    <br/>
    <p>you have a possible lead from </p>

    <br/>

    <ul>
        <li>Name: {{$from_name}}</li> 
        <li>Email: {{$from_email}}</li>
        <li>Phone: {{$phone_number}}</li>
    </ul>    
</body>
</html>
