<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tailwind Page</title>
</head>
<body style="background-color: #f7fafc; padding: 0.5rem; margin: 0; display: flex; justify-content: center; align-items: center; color: #2d3748; width: 100%;">
<div style="margin: auto; padding: 1rem 0; background-color: #ffffff; height: 100%;
        border-radius: 0.375rem; align-items: center;
        gap: 1rem; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); color: #2d3748;">

    <div style="width: 100%; display: flex; justify-items: center">
        <img src="https://kejamove.com/wp-content/uploads/2021/06/Kejamove_Logo1x.png" style="height: fit-content; width: fit-content;margin: 0 auto">
    </div>

    <h1 style="font-size: 2.25rem; font-weight: 700; margin-bottom: 1rem; color: #2d3748; text-align: center;">Your Moving Quotation</h1>
    <h2 style="font-size: 1.25rem; font-weight: 400; margin-bottom: 1rem; color: #2d3748; text-align: center;">Hello, {{$invoice->client_first_name}} {{$invoice->client_last_name}}</h2>

    <p style="font-size: 1.125rem; text-align: center; max-width: 75%; margin: 0 auto; color: #2d3748;">Thank you for inviting Keja Move Ltd. Our quotations are based on the distance between origin and destination locations as well as volume of your inventory.</p>

    <div style="height: 1rem;"></div>

    <table style="width: 75%; border-radius: 4px; display: table; height: fit-content; margin: 0 auto; background-color: #eff1f3; color: #2d3748;">
        <thead>
        <tr>
            <th style="padding: 8px; text-align: start; border-bottom: 2px solid #e2e8f0; color: #2d3748;">MOVING FROM</th>
            <th style="padding: 8px; text-align: start; border-bottom: 2px solid #e2e8f0; color: #2d3748;">MOVING TO</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="padding: 8px; text-align: start; border-right: 2px solid #e2e8f0; color: #2d3748;">{{$move->moving_to}}</td>
            <td style="padding: 8px; text-align: start; color: #2d3748;">{{$move->moving_from}}</td>
        </tr>
        </tbody>
    </table>

    <div style="height: 1rem"></div>

    <div style="background-color: #f98d11; color: white; width: 100%; padding: .1rem 0">
        <h2 style="text-align: center">
            HOW IT WORKS
        </h2>
    </div>


    <div style="width: 100%; display: flex">
        <div style="display: flex; flex-direction: column; gap: 4px; border-right: 1px solid gray; padding: 14px ">
            <img style="width: 100px; height: 100px; margin: 0 auto"
                 src="https://ci3.googleusercontent.com/meips/ADKq_NYuG2HINvQvIP5Cy3ZJ4iyG8ad_O-GuOdmi06jtPaOxze7P5nJXxwq-HOvnlC8XKPDVV_Dccl1GWwD92RbiXI8dVpXFM3QBIxHsSXlmmX5s8PuPIABsyYAHkA=s0-d-e1-ft#https://kejamove.com/wp-content/uploads/2018/08/kejamove_packing.png"
                 alt="packing.png" class="CToWUd" data-bit="iit">

            <div style="width: 70%; font-size: small; text-align: center; margin: auto">
                WE PROVIDE
                PACKING MATERIAL
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 4px; border-right: 1px solid gray; padding: 14px ">
            <img style="width: 100px; height: 100px; margin: 0 auto"
                 src="https://ci3.googleusercontent.com/meips/ADKq_NYri9ugSFmB-3krx9FAGyqj7HDPcGBY-hCZ10b3PehCDIfR7JWWidvf9Ih0daV2eVNsYYuFil7-_YZN3nRFKa5nZmJzPjoW6JJbU1wGdIsSlINoIiVsvJ3a=s0-d-e1-ft#https://kejamove.com/wp-content/uploads/2018/08/kejamove_movers.png"
                 alt="packing.png" class="CToWUd" data-bit="iit">

            <div style="width: 70%; font-size: small; text-align: center; margin: auto">
                OUR MOVERS PACK AND
                WRAP
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 4px; border-right: 1px solid gray;padding: 14px">
            <img style="width: 100px; height: 100px; margin: 0 auto"
                 src="https://ci3.googleusercontent.com/meips/ADKq_NbrOSDVWnrb6ElSANJ9BsrH-NX1vDzQpCC9fwjGPCltwkZ5vbGiqXw8Fav3Kc1gNmCc80H-u7agQvngOOA0-3Y4-9jojAu3nnl2kZT6FOAOzstFTUjdv1Fr=s0-d-e1-ft#https://kejamove.com/wp-content/uploads/2018/08/kejamove_trucks.png"
                 alt="packing.png" class="CToWUd" data-bit="iit">

            <div style="width: 70%; font-size: small; text-align: center; margin: auto">
                WE LOAD, TRANSPORT
                & OFFLOAD
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 4px; padding: 14px ">
            <img style="width: 100px; height: 100px; margin: 0 auto"
                 src="https://ci3.googleusercontent.com/meips/ADKq_NbvNNDl3EIE1AGfJyjheL0s8SSeIjplqOVcuEtSMr-eXn4aMp371oLDYiiy4P-p6TYLVUWuPk_mPWRpKiHlzDpV4B1rbmNiBpXgscLyWP_ynuzP26A8ciekV2EiJQWY=s0-d-e1-ft#https://kejamove.com/wp-content/uploads/2018/08/kejamove_full_service.png"
                 alt="packing.png" class="CToWUd" data-bit="iit">

            <div style="width: 70%; font-size: small; text-align: center; margin: auto">
                SET-UP THE
                DESTINATION HOUSE
            </div>
        </div>
    </div>
    <div style="background-color: #f98d11; color: white; width: 100%; padding: .1rem 0">
        <h2 style="text-align: center">
            YOUR QUOTE
        </h2>
    </div>

    <div style="height: 1rem"></div>

    <div style="padding: 14px; text-align: center; width: 100%; font-size: large; color: #f98d11">
        Ksh {{ $move->invoiced_amount }}
    </div>

    <div style="height: 1rem"></div>

    <p style="font-size: 1.125rem; text-align: center; max-width: 75%; margin: 0 auto; color: #2d3748;">
        We require a 50% deposit payment at least 3 days in advance of your move and the remaining 50% immediately upon completion of work.
    </p>

    <div style="height: 1rem"></div>

    <p style="font-size: 1.125rem; text-align: center; max-width: 75%; margin: 0 auto; color: #2d3748; font-weight: bold">
        How to pay: Lipa na Mpesa (Paybill Number: 400222 > A/C Number 68910#1)
    </p>

    <div style="height: 1rem"></div>

    <div style="background-color: #f98d11; color: white; width: 100%; padding: .1rem 0">
        <h2 style="text-align: center">
            YOUR QUOTE
        </h2>
    </div>

    <div style="font-size: 1.125rem; text-align: start; width: 100%; margin: 0 auto; color: #2d3748;" >
        <ul>
           <li>
               Be present to receive and guide the move crew on the move day at the agreed time or appoint a suitable representative.
           </li>
            <li>
                Ensure the destination premise is ready e.g no wet paint, the premise has been cleaned/fumigated and there are no other ongoing jobs at the premise on the move day e.g. construction, plumbing, electrical, cleaning, etc. Presence of other workers may compromise the exercise and will reduce accountability.
            </li>
            <li>
                Pack your personal/sensitive items in the boxes/crates that will be provided. Please ensure that all personal documentation and valuables are locked away for the duration of the move as Kejamove will not be responsible for packing these items in error.
            </li>
            <li>
                If applicable notify the Premise Management, caretaker, landlord or securitypersonnel that you will be moving to avoid any unnecessary delays due to lack of communication otherwise waiting charges will apply.
            </li>
        </ul>
    </div>


    <div style="background-color: #f98d11; color: white; width: 100%; padding: .1rem 0">
        <h2 style="text-align: center">
            PLEASE NOTE :
        </h2>
    </div>

    <div style="height: 1rem"></div>

    <div style="font-size: 1.125rem; text-align: start; width: 100%; margin: 0 auto; color: #2d3748;" >
        <ul>
            <li>
                Currency, Cheque books, Bonds, Jewellery, Watches, Ipads, Mobile Phones and the like will not be packed by Keja Move.
            </li>
            <li>
                Overtime charges shall apply for delays caused by yourself such as the new premise not being ready, not getting clearance from the landlord / management amongst others.
            </li>
            <li>
                Should any damage or loss of items occur, this must be reported within 7 days after delivery and unpacking.
            </li>

        </ul>
    </div>

    <div style="height: 1rem"></div>


    <div style="font-size: 1.125rem; text-align: start; width: 100%; margin: 0 auto; color: #2d3748;" >
        Please note: This quotation does not include customs clearance charges, toll fees and/or ferry charges where borderlands are involved.
    </div>

    <div style="height: 1rem"></div>


    <div style="font-size: 1.125rem; text-align: start; width: 100%; margin: 0 auto; color: #2d3748;" >
        Please note: Fumigation, Dstv Installation, Carpentry, Masonry, Plumbing, TV, Chandelier and Picture Frame Mounting can be done on request at an additional cost.
    </div>

    <div style="height: 1rem"></div>


    <div style="background-color: #128ced; color: white; width: 100%; padding: .1rem 0; border-radius: 4px ">
        <h2 style="text-align: center">
            Contact us on 0723 474252 / 0711 931212 or reply to this email
        </h2>
    </div>

    <div style="width: 100%; display: flex">
        <div style="display: flex; flex-direction: column; gap: 4px; border-right: 1px solid gray; padding: 14px">
            <img style="width: 100px; height: 100px; margin: 0 auto"
                 src="https://ci3.googleusercontent.com/meips/ADKq_NZ5-YEhA82ty3uVlQRGfO-sZMnohIrdbt7vjfNFaUzZ36GDu-28m8K4_P8v59CW0nhvHwCpIBN5ezBZ87uitqFShCjkA0rsdjBLKx-JT-bEp9Xw=s0-d-e1-ft#https://kejamove.com/wp-content/uploads/2018/08//csr_icon.png"
                 alt="packing.png" class="CToWUd" data-bit="iit">

            <a href="https://kejamove.com/" style="width: 70%; font-size: small; text-align: center; margin: auto; text-decoration: none; color:#128ced ">
                Customer
                Service
            </a>
        </div>
        <div style="display: flex; flex-direction: column; gap: 4px; border-right: 1px solid gray; padding: 14px ">
            <img style="width: 100px; height: 100px; margin: 0 auto"
                 src="https://ci3.googleusercontent.com/meips/ADKq_Nacn4n691BxsLBuQDNGu7NE-YbAePnCy4refLgCKr0_umJgH2piz5jL-8wnrlhNwXPBEiBTORvQYrRdq2dndBCtsAIcXgc9sa9sNAEFCcCsCa92DD8m_A=s0-d-e1-ft#https://kejamove.com/wp-content/uploads/2018/08/shipping_icon.png"
                 alt="packing.png" class="CToWUd" data-bit="iit">

            <a href="https://kejamove.com/" style="width: 70%; font-size: small; text-align: center; margin: auto; text-decoration: none; color:#128ced ">
                Timely Arrivals
            </a>
        </div>
        <div style="display: flex; flex-direction: column; gap: 4px; border-right: 1px solid gray; padding: 14px ">
            <img style="width: 100px; height: 100px; margin: 0 auto"
                 src="https://ci3.googleusercontent.com/meips/ADKq_NYnn_lbSVaZ1zh2ADl0MbgnWcXuxCBJGiHROqYbWNxz3J2ULglDwAOnT0JOohEvzHe11if6snvWhxcKU-aizI7RU1aKjF74m35jt46Mo3VmlQCq4E3en9g=s0-d-e1-ft#https://kejamove.com/wp-content/uploads/2018/08/moneyback_icon.png"
                 alt="packing.png" class="CToWUd" data-bit="iit">

            <a href="https://kejamove.com/" style="width: 70%; font-size: small; text-align: center; margin: auto; text-decoration: none; color:#128ced ">
                Satisfaction Guaranteed
            </a>
        </div>
        <div style="display: flex; flex-direction: column; gap: 4px; border-right: 1px solid gray; padding: 14px ">
            <img style="width: 100px; height: 100px; margin: 0 auto"
                 src="https://ci3.googleusercontent.com/meips/ADKq_NaLuSz1bbkMGXVhUazJgP-GX1KbBB2hBKyNT6FNa3l8rp5LKrj0otXe9NP9ycWvTooLzfnl41dVf3lrnC6aJnmzz8nqsguqqtLpd2pak9xCQvNyMa0=s0-d-e1-ft#https://kejamove.com/wp-content/uploads/2018/08/return_icon.png"
                 alt="packing.png" class="CToWUd" data-bit="iit">

            <a href="https://kejamove.com/" style="width: 70%; font-size: small; text-align: center; margin: auto; text-decoration: none; color:#128ced ">
                Insurance
                (on-demand)
            </a>
        </div>
    </div>

</div>
</body>
</html>
