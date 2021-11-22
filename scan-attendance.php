<?php
	// Validate if the admin logged in
    include 'validateLogin.php';

    // Initialize Title Page variable
    $eventTitle = "";

    // Using database connection file here
    include 'dbConnection.php';

    // Check if it fetched correctly from the events page
    $scanAttendanceErrorFlag = false;
    if (isset($_GET['eventID'])) {
        $eventID = $_GET['eventID'];
        $eventStmt = $conn->prepare("SELECT * FROM `events` WHERE `admin_ID` = ? AND `ID` = ? AND `status` = 1");
        $eventStmt->bind_param('ii', $id, $eventID);
        $eventStmt->execute();
        $eventInfo =  $eventStmt->get_result();
        $eventStmt->close();
        if ($eventInfo->num_rows > 0) {
            while ($row = $eventInfo->fetch_assoc()) {
                $eventID = $row['ID'];
                $eventTitle = $row["event_title"];
            }
            $scanAttendanceErrorFlag = true;
        } else {
            $eventTitle = "ERROR!";
            $scanAttendanceErrorFlag = false;
        }
    }else{
    	$eventTitle = "ERROR!";
        $scanAttendanceErrorFlag = false;
    }
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $eventTitle;?> - Scan Attendance | Attend and Certify</title>

	<?php 
        include 'style/style.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file    
    ?>
    <!-- Custom Styles -->
    <style>
        @media only screen and (max-width: 280px) {
            .header-title {
                font-size: 2rem;
            }
        }
    	.container-scan-barcode {
            /*background-color: white;
            border: 10px solid #929eaa!important;*/
            border-radius: 25px;
    	}
        #snackbar {
            visibility: hidden;
            min-width: 250px;
            margin-left: -125px;
            /*background-color: green;*/
            color: #fff;
            text-align: center;
            border-radius: 10px;
            padding: 16px;
            position: fixed;
            z-index: 1060;
            left: 50%;
            right: 50%;
            top: 100px;
            font-size: 17px;
        }
        #snackbar.show {
            visibility: visible;
        }
        .form-control-custom {
            display: initial;
            width: 5rem;
        }
        .modal {
            overflow-y: auto;
        }
    </style>
    <!-- Datatables Styles -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="style/jquery.dataTables-custom.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/3.3.3/css/fixedColumns.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    
    <!-- Datatables Scripts -->
    <script src="scripts/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/3.3.3/js/dataTables.fixedColumns.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
</head>
<body class="d-flex flex-column">
	<?php
        // Initialize Active Page for Navbar Highlight
        $activePage = "events";

        // Navbar Model
        include 'model/navbar.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file    
    ?>
    <div class="main-body container-fluid flex-grow-1 mt-5">
        <!-- The loading modal -->
        <div  id="loadingModal" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1" style="z-index: 1060;">
            <div class="modal-dialog modal-dialog-centered modal-sm " >
                <div class="modal-content border-form-override p-3 text-center" id="myModal">
                    <div class="text-center">
                        <div class="d-flex justify-content-center">
                            <img alt="Loading" style="width: 6rem;" onContextMenu="return false;" ondragstart="return false;" src="data:image/gif;base64,R0lGODlhyADIAPcAAAAAAAEBAQICAgMDAwQEBAUFBQYGBgcHBwgICAkJCQoKCgsLCwwMDA0NDQ4ODg8PDxAQEBERERISEhMTExQUFBUVFRYWFhcXFxgYGBkZGRoaGhsbGxwcHB0dHR4eHh8fHyAgICEhISIiIiMjIyQkJCUlJSYmJicnJygoKCkpKSoqKisrKywsLC0tLS4uLi8vLzAwMDExMTIyMjMzMzQ0NDU1NTY2Njc3Nzg4ODk5OTo6Ojs7Ozw8PD09PT4+Pj8/PzxDTDlIWDZMZDNQbzBUei5XhCtajSldlSdgniRipSJlrCBnsh9puAx05AF7/QF7/gB7/gB7/gB7/gB7/gB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wR9/wd+/wqA/wyB/xCD/xOE/xaG/xmI/x6K/yKM/yiP/y+T/zaX/zya/z+c/0Oe/0Wf/0eg/0qh/06j/1Gl/1an/1yq/2Gt/2Wv/2ix/2uy/2+0/3G1/3S3/3i5/3u6/327/3+8/4C9/4K+/4W//4nB/4zD/5DF/5fJ/53M/6nS/7HW/7ja/8De/8fi/87l/9Lo/9Xp/9fq/9rs/97t/+Dv/+Pw/+by/+nz/+z1/+72//H3//P4//b6//j7//n8//v9//z9//3+//3+//z9//z9//z9//z9//z9//z9//z9//3+//3+//7+//7+//7+//7+//7+//7+//7+/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////yH/C05FVFNDQVBFMi4wAwEAAAAh+QQJAwC8ACwAAAAAyADIAAAI/gB5CRxIsKDBgwgTKlzIsKHDhxAjSpxIsaLFixgzatzIsaPHjyBDihxJsqTJkyhTqlzJsqXLlzBjypxJs6bNmzhz6tzJs6fPn0CDCh1KtKjR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/wB7/wB7/wF7/wF7/wJ7/wJ8/wJ8/wN8/wR9/wV9/wd+/wqA/wuB/w6C/xCD/xOE/xWF/xeG/xmI/xuI/x2J/x+L/yKM/yaO/ymQ/y2S/y+T/zKU/zWW/zmY/zuZ/z6a/0Gc/0Od/0Sd/0We/0af/0mg/02i/1Kl/1an/1up/1+r/2Ou/2iw/26z/3O2/3e4/3u6/3+8/4S//4jB/4zD/47E/4/F/5HF/5LG/5TH/5fJ/5nK/5vL/5/N/6LP/6XQ/6fR/6vT/6/V/7PX/7bZ/7rb/77d/8Hf/8bh/8zl/9Ln/9Po/9Xp/9bq/9jr/9rs/9zt/97t/9/u/+Hv/+Pw/+bx/+jz/+nz/+r0/+v0/+z1/+71/+/2//D3//L3//P4//T5//b6//j7//r8//r8//v8//z8//z9//z9//39//39//3+//7+//7+//7+//7+//7+//7+//7+/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////wj+AGUJHEiwoMGDCBMqXMiwocOHECNKnEixosWLGDNq3Mixo8ePIEOKHEmypMmTKFOqXMmypcuXMGPKnEmzps2bOHPq3Mmzp8+fQIMKHUq0qNGjSJMqXcq0qdOnUKNKnUq1qtWrWLNSvYQI0Bw2ZbhcoeIkRw4mUKpg2SJGTZw+hiJpjRrKkBwzV5SY3cu3r1++W9bsQTT36KM+bLT8Xcy4cY4ucA4V9qnKEBsqjjNr9vskzR9Nk22KGqTmyebTqPlmCi1TUpuyqWOf9sL65apBZYjI3r0ZT22WnOhY4U1cM6XfKRupSVK8eeMuyE9mYnPEufXFdaKTJGWnyfXvfo/+aw9JaDj482a5jAfpiAz69znmrPcoBwn895Lmb8Qk5v77LfppVAgU/r0nR4AXkdJGEAXih2BFkSjWIHpaPEjR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/zWW/zeX/zmY/zuZ/zya/0Cc/0Od/0ef/0ui/1Gl/1an/1qp/16r/2Kt/2Wv/2qx/2+0/3G1/3K2/3S3/3e4/3m5/3y7/4C9/4W//4fA/4rC/4/E/5HG/5bI/5nK/5zL/5/N/6LO/6XQ/6rT/7HW/7ja/77d/8He/8Tg/8fi/8rj/8zk/8/m/9Dn/9Ln/9Tp/9bp/9jq/9rr/9vs/9zs/93t/97u/+Dv/+Lv/+Pw/+Tx/+Xx/+fy/+nz/+r0/+v0/+31/+/2//P4//X5//b6//j7//n7//r8//r8//r8//v8//v8//v9//z9//39//7+//7+//7+//7+//7+//7+//7+//7+/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////wj+AHMJHEiwoMGDCBMqXMiwocOHECNKnEixosWLGDNq3Mixo8ePIEOKHEmypMmTKFOqXMmypcuXMGPKnEmzps2bOHPq3Mmzp8+fQIMKHUq0qNGjSJMqXcq0qdOnUKNKnUq1qtWrWLNOxaRokB44acZ84XJlCg4cWL6cYQMHT59Bhx5plXqJkJwxTM7q3cu3r14mYNzwOeRp7lFEetRU8cu4seMqZ+YYMmW4Z6lCbcw63syZcRM1gUBVtgkKUJq8nVOr5muEzJ5Io2EiUnNkte3be8MIOhU75SlAXnALH24FT+HeIzXVoTK8+XAlbBQh/ygqjhLn2JuXcTRd4yk9UbL+ix9upA2m7hVjCcoyvv1wJnRGoY+YKLj7+8KrEJrfkJMa/AAOp0Yn/CU0iGYBJmjbFIEUWNAoaygoIW5nnOcgIlhMqKFtTTTCHypzGLHhiKmhEct8oYRB4oqbVRHKfJSwx+KMfh0yHyJS0KjjXnPMJwiLZLAhxx6CHLJIJJrINwolihgSiB5ztJGGFQCCMd8dGiLxxRuAOPJKRZYI4sYW4z1xCXpwSCgF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lMf/msr/ncz/oc7/ptD/q9P/r9X/sdb/tNj/t9n/vNz/wN7/xOD/yOL/zOT/z+b/0+j/1+r/3O3/3+7/4e//4vD/5PD/5vL/6PP/6vT/6/T/7PX/7fX/7fX/7vb/8Pb/8ff/8/j/9Pn/9fn/9vr/9/r/+Pv/+fv/+vz/+/z/+/z//P3//f3//f3//v7//v7//v7//v7//v7//v7//v7//v7//v7/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////CP4AfQkcSLCgwYMIEypcyLChw4cQI0qcSLGixYsYM2rcyLGjx48gQ4ocSbKkyZMoU6pcybKly5cwY8qcSbOmzZs4c+rcybOnz59AgwodSrSo0aNIkypdyrSp06dQo0qdSrWq1atYs1K19EiRoUF+9MhhY8aIkTBm0rB5Q8dPIUWTNmmN2glSIT5tupjdy7ev371d3gBapGmuUVWOAsUR87ex48dk7BCSZNgnKER1vDzezNmxGD6MVFW2eYnQGyydU6v226UOolCjX7ZK1Ga17dt8teCJFFtlJ0FjcAsfzgYRq94kJenZMrz58DGCOiH/WOmN8+vNuQASNV2jJj3Yw/43D1PoeHeKogBxEc9+OJpFtc5HVMS4vX3hbSbJb8iJzv3/w+Eh3X4H1YIIGAAmiFsYixBY0CbWKSjhbXmc4qAvimg24YarnUGZfLAAwuGIq2ERyCvdgRIhiSx29gZsvVVSRos0dnYGJrEpsl6NPD4Whn6GEcIiGXTwEUghiDgySSal+FIKJ5dQAkkihQCiBxwtKjKXiBum0UcimVQkiyWI9MEGcxMSktUfCmLBxh+LgOLRKYvsgaCCf1zlR4JgAFJYSZD8QUaCglBVyx4ApmEIKiq5skgcAKop1Z73xdHILC9dAkgY9x0SlSH3pfGhTKgIoiF7Wjr1CGrsgVEILP43ddKHFuxhwRtTl5wa3h5y6nSJf+KFEaZS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/P3//Pz/+/z//Pz//P3//f3//f3//f7//v7//v7//v7//v7/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////CP4AeQkcSLCgwYMIEypcyLChw4cQI0qcSLGixYsYM2rcyLGjx48gQ4ocSbKkyZMoU6pcybKly5cwY8qcSbOmzZs4c+rcybOnz59AgwodSrSo0aNIkypdyrSp06dQo0qdSrWq1atYs059NGjPHTVjwHTRQsXJjBlRsnQBY0YOnj6JKGmVqkhPmy9n8+rdy1fvEi5n8BDqNPfoITtg+ipezHjGFzl7HhX2aWlPGimNM2vmq4VOIVyTbX7qU+bI5tOo81JhAwhUaJiJ3EBJTbu2lDqRXquchIdL7d+/j5gJpLskpDVIgCsHviWPpuIfI61ZTl35EjaNoGuU9CZJ9e/Akf7MyaS9Iig8S8CrBx4lT6nyEQ1tWU+fuSD4DSupMVK//28zjuCHEC56ROHfgbUlkYeABU0iBoIQ1kaGXAwGglmEGKJGxSD4gTIHfxmGuNkRdbgGXSNeiKjiaV9AUtwfK8a4GRSIvLaHjHtV0YUYXFCB41lKADIZHipWoQYeeviBiCPkGaTJI4n4cYcZWqi4x1x0ZAjFGXlkVxEnhORxRhMY4oFVKWdAuAQZeCiCSkegBBJHFhC+ARpVnYxx4BFj7EEYSYvcAQaI9blBVSl69rfFHZKs1EgaptV3x1Rp9BfGHzA5Aml9C0J1R31kcDiTI2jU18dTN6pnhBmJ4IRIYv7qHXEfU4JE+h0XNe7EB53gLdGqUouk910Sd5jIUyd3kPldFJMkpckV4IkRYFCRwFpdGK4glWZ1R0xKVCl22LqcHUelSl0UohpViI/VpSvUI8Iu50VuSU2C17nNCgVKitSR8QlTnZRxbbZB2VEdGsYuhUuW1JkJFCPi/rbGnU/pQehvStDr04PjUsUHdWb8BONydFiVB3VC8gTKfMqZQTFVBit3xZ86EakcGP9ixcZydex0ibK/bXGJVrhw/BsSjebkhnJSuDhXJVYo90ZOjSxH3GSHLJe0TWoox4ZuNv8Wx02TJPcbFpwUZzRtSNsU82+GQPeId7/NUVMns/1Wsv52nx69dUwn/1ZFztCtDBy5MuFS5W9Xwpe14C+75AdwXjC4bW2zwmQGcLnixwhwaMSkCXBnMCjQ5akp8dxL5tK2iOm8fP5bpy4JXNsYsJ/+2xcvjf5byrAvgnG+LLWO2ha5D7R2Zk6g0QfNLNlOW+PJ66HZE2n4QbhLmpidGhMJw76J93xBocYf4b80eW1qJE8Q6mdJsUYg6cdUx29Xu8/L+jNQ0YYg78nJ8jZDBf0RhAtyIERPXKGE2sjBgENRxG8OAUGhBC41UaigUCpFm5BpECgsS43DPtgTUPymcyTcCSQSVL8U2qQQtRGDC3vyMdo8cIY7CRtqRohDnMyhNv6n6mFO4LeZXwnxJtZCDYWOaJMQooaJOFkcapAAxZtggTZOqKJNqkCbKWixJhdCDRa+SJO8HY+MMwHaabSARpk0MDVNaGNMzIga6MlxJU48zd/uqJIw1OZ1fGTJ5mjjrkCiZDq00YMhV3I/2thtkSnR4WlwB0mU8A81GazkSR7xmz1qUiTxQk3+PjmSJJ4GcaQcCSJTw7tUjuSCqVmiKz8yCMbNMiSciNhmQndLkAxQM5nspUf6Rhs/CNMjhvjPMeEUytMgARPL5MggaUO7aGIElqipnDUz4gjgxG2bGLkXbcgAzmsCx4jlnAgmyIcaD6aTIkQ8DSDfKZFLpoaX9P6MiCvCSBtF5FMiDKsNF1r4T4RAQpen8VZBHcLBBElmoQ1xxMVYSVCIEqShtdmbRRUiUeUAb6MIKRVwnqAxkBpEdsD5QkU3ujPloIFgJi0IJvhZmzTE9CDGq80jb0qQX6ZGoTzlxdyo07OgCoSYyjnD9mIKCnHezBJGjcQTqqOFh/IUEN95AvVu2sjqnAGaNwWFH79DhcyZdBIG+o4R0iDLjSaCblnFQwBBCgiEKocLCjRpTg/GCJNiEzxkGOVCu0ofLuhhqfnEKH2gkIY/ILacrugagrCnvY58YhB2EEPpoIjUAzXPD3aMCCcCYYcwwBUJq2PiXg+0hTLEIQ+AUP7EI1I7kEkkog94oMMZwMBFxfBBi4F4I46owIUwbEG4m9lsFRVB0x+lLrRClMRYnQtEMqLiDnalLmPw+cVC9Fa7m1HCY5l4CemBNzOYkmMekHtexbTvjo+YZnv70oS5ynEQeZxvXgTbxlLkIa36zcvXFpmJOLCzvVGIHB8bIdkAF9KQlLBDc6l7w09+Qg/5/VEBXYmLPyRKuxS8JSTwIIbsZkijvZyEHsoA1wxJIQ17gKo1OzGIO4yBvfQJgx1CnM9SJAIPZpgw277ABj0owr4bvUQjCMGHPNRBDWUAAxeykFYnUEELaxmDGu6wB0JY1ahgDrOYx0zmMpv5zGhOsxKa18zmNrv5zXCOs5znTGejBAQAIfkECQMAtwAsAAAAAMgAyACHAAAAAQEBAgICAwMDBAQEBQUFBgYGBwcHCAgICQkJCgoKCwsLDAwMDQ0NDg4ODw8PEBAQEREREhISExMTFBQUFRUVFhYWFxcXGBgYGRkZGhoaGxsbHBwcHR0dHh4eHx8fICAgISEhIiIiIyMjJCQkJSUlJiYmJycnKCgoKSkpKioqKysrLCwsLS0tLi4uLy8vMDAwMTExMjIyMzMzNDQ0NTU1NjY2F12nAXn6AHr+AHr+AHr+AHr+AHr+AHr+AHr+AHr+AHr+AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AXv/AXv/Anz/A33/BX3/B3//CX//CoD/DIH/D4L/EYP/EoT/FIX/F4b/Goj/H4v/JY7/KZD/LJH/LZL/L5P/MJP/MpT/M5X/NZb/Npf/OJj/PZr/P5v/Qpz/RZ7/SJ//TKH/T6T/VKb/V6f/Waj/W6r/Xav/YKz/Y67/arH/brP/c7b/eLn/e7r/frz/gL3/g7//h8D/icL/jMP/j8X/k8f/mcr/n83/os//p9H/r9X/ttn/u9v/wd//xuH/yOL/y+T/zuX/0ef/1en/1+r/2ev/2+z/3u3/4e//4/D/5fH/5/L/6fP/6/T/7fX/7vb/8Pf/8ff/8vj/9fn/9vr/9vr/9/r/9/r/+Pv/+fv/+vz/+/z//f3//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////CP4AbwkcSLCgwYMIEypcyLChw4cQI0qcSLGixYsYM2rcyLGjx48gQ4ocSbKkyZMoU6pcybKly5cwY8qcSbOmzZs4c+rcybOnz59AgwodSrSo0aNIkypdyrSp06dQo0qdSrWq1atYs07NlEiQHTVlwHTZkoWKECFUsmzpAsaMGjuCEmXSGnUSITtpsJzdy7evX75Z0twhNImuUVWPBMHZ8rex48db4gh6pMpwT06E3FR5zLmzYytvCHGybNNSIDVPPKte7ReKGkGWSL/s5GcM69u4+5Lx00l2ykp3zOYePrzKndi+Rzp6k5q48+FP4DhK7lHVITTPsz9Pg4i6xkZhtP6Lfz6mkfeKmug0Gc+eeJM6ms5DTAXISvv7xK8ISiWfoaIu+AVIXBeL9IdQKXYIqCBxd5RiIEGReLHghLl5UdiDgwhH4YarUUFIf5+4weGIt73xiXeNcEHiiqtx8Uhyh0DB4oyeRdEdaYWsR+OOj0FhiGWDkGhFGG7gEQgiizgyySW93dLJJZM4ssghgeDhRhj2jTgIXYFsiMUbg1xo0SSDvKEXhYFkpceEXvhh3keN+AHggn1cdYeCWOAxXUmN4HGFgnhU5YeAbhzC0iFtCOjHVIbgJ0UdksAkCR1R4FdIVIk0N94VecQ3EyZ5/MneEzc21YgUo9rhCU6d1KGjeP5SMNKUJFmK58WeOjEi4XhWRLLUJYyJRwUg/PWUyh8aZrfFJUmZEp54X1AilCRfjCcGKkjhMZ4dDg5VSh3j5XFUIsL+eJQhqGqnSFGaZKGdFpAo5Yi72Wkx2lBqaNcFckpVMudzbQzVZXZnNMlUJ2doJ0hQllT6nBvdOlWKiM9NgQlQb2RXB1XgPgfHT4tk90ZlU6mS8XPr8pTKv8OtgW1VqOTrnBck6zQwcWWMghUoYjy3sE6abDbgvVhtwjJuVxB9U8fFVWIYJUIPZ0dOlLyam6GWHeJcE9Iu7VygsiVI3NQ2aeJwbmO8TNopz+YWhdIy5UGcFcwmBzVxe9T0Sf7UuF3qXZDDXREKTX8QR4Z8qoBBXJoymUIvbk+8KF8jxG2h9kuFMPig2Ln5DdMaw2VxooGc1HrbGjFxoultjD9YeG5PbAKTIMNZMfiDt+w93M8uYZdb3rgLtGZuabxkyXBSwG2g2cNd3BIgw90RPEGcs+6Sbbl1Pf0tkAxnRkubDHf49gMpnpvsK2mdW+vkD3o1S9rmNhf5Ah2fG9gqlZGbGvQT5PttZ1jJKKy2GvbRz323gYLOUkKu3OCqf4wYTspQwofcVKFY/bsFKtJ1mzqlhGK3cUMGCcKG3LxBJea7DSBGOBAEsiYMKjkba97EwgjiRgopucRwMDjCUDQPJf6KyA0XWEiQYN2mQCchRG5QR0SBpCE3HzqJ3HBDtiYyjTV6QAkdcrPCJt7ChavZ2ElOdhusNbFRuPnYSUCHG1l5MYi4CdhJEoYbyTXREblBA0qqhRsxEVESuREDSlSEG+c10X636QJKRHWb0TUxfLjRAkoIqBoesrAUuZkCSobjxYHs4ZOgDKUoQ8mHRebGYJ3UCSFvU7dU6mRXt4mUK3WiP9w8cJY2eSJuJohLmyQKN+bqpU3gkJtFCdMmd8KNGI9JE9rhpnjMpEkDbzPEaM6kEsO5nTVjIsPVuHGbMEkha/4AzpiQkTVyLKdL+mBBS6ozJXC05TtbggoZ4caA8/5Eyf9Yw798rmR4uOGXP0+CiOEYc6An+QQlPeMFhKZEl3V06EmcqUyJmgSSuIlCKy0qEpnhBn8cDQngbqi8kHJkE6tjDfBMChI24qYKhmRpRzI3nDjI9COOIw4Sb8oRMLKmC6fgKUc4wUHckFOoG0lmbqQQL6RmpGoDAoVTM0JM4qhxqhZ5xHOiiNWKgHCpTe3qRCrRTWrOT6wSAehwviBVtEYEFFp4jhou59aGoNE5I6srRPYpNb0+BBIpzQ0c6OpXhOxBO2vQZmERkgq+DqcMJV3sQDDBSOd0IaaSLUhB35WIzCJEqc95gh7cmVlT8FE7ZsCsZyWRrOdYQRA18/7sLRJhT/GMwY6yPcRCifOEOzjSs0psTxb68FvJ3ow9VtAD+jKrVvZQAQ9nXSxo77MGQajWra9T0BncVFhC1BZPYNKeWBHR2gVZQQxuuMORkrQkVJqUEabjEU8jYUT58lQTEN0RUlXxh7KSaKop0u9UQ0QjsRKivBRCayXOuaG6/mdEek2FIM40ocJ24g6BvY9kKVEH/47Hs5gIToBkewtN6CG+2iGxQGhz2hSreCCR2MPROPnigjgiD6vMTY0REolBxKG+qtnxQiJBiDmAAcF+EfJDMrGIQvBhDmARC1nMouQqW/nKWM6ylrfM5S57+ctgDrOYx0zmMpv5zGhO8wNPAgIAIfkECQMAxAAsAAAAAMgAyACHAAAAAQEBAgICAwMDBAQEBQUFBgYGBwcHCAgICQkJCgoKCwsLDAwMDQ0NDg4ODw8PEBAQEREREhISExMTFBQUFRUVFhYWFxcXGBgYGRkZGhoaGxsbHBwcHR0dHh4eHx8fICAgISEhIiIiIyMjJCQkJSUlJiYmJycnKCgoKSkpKioqKysrLCwsLS0tLi4uLy8vMDAwMTExMjIyMzMzNDQ0NTU1NjY2Nzc3ODg4OTk5Ojo6LUlnD2rMA3fzAHr+AHr+AHr+AHr+AHr+AHr+AHr+AHr+AHr+AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AXv/Anv/Anz/A3z/BH3/Bn7/CH//CoD/C4H/DoL/EIP/EoT/FYX/GYf/HIn/Hor/IIv/IYz/JI3/KI//LpL/M5X/OZj/PZr/QZz/Qp3/RJ3/RZ7/Rp7/R5//SaD/TqP/UqX/Vqf/Waj/XKr/YKz/ZK7/abH/bbP/c7b/eLn/fbv/gL3/g7//iMH/i8L/jMP/jsT/j8X/ksb/lcj/mMn/msr/nsz/os7/p9H/qtL/rdT/sNb/s9f/t9n/u9v/vt3/wd//xeH/y+T/zuX/z+b/0ef/0uf/0+j/0+j/1en/1+r/2ev/2+z/3u3/4O7/4u//5PH/5/L/6vT/7PX/7/b/8vf/9Pn/9/r/+Pv/+fv/+vv/+vz/+/z/+/z/+/z//P3//P3//f3//f3//v7//v7//v7//v7//v7//v7//v7//v7//v7/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////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M/8lDmumiCfwAGC+SDXOEgkFnf2iZjxPBBWJeAOhQOrn9C4CDgcttKIXzehGO/rRkI60pCdN6Upb+tKYzrSmN83pTnv606AOtajPGhAAIfkECQQAygAsAAAAAMgAyACHAAAAAQEBAgICAwMDBAQEBQUFBgYGBwcHCAgICQkJCgoKCwsLDAwMDQ0NDg4ODw8PEBAQEREREhISExMTFBQUFRUVFhYWFxcXGBgYGRkZGhoaGxsbHBwcHR0dHh4eHx8fICAgISEhIiIiIyMjJCQkJSUlJiYmJycnKCgoKSkpKioqKysrLCwsLS0tLi4uLy8vMDAwMTExMjIyMzMzNDQ0NTU1NjY2Nzc3ODg4OTk5Ojo6Ozs7PDw8PT09Pj4+NElgDm3TA3j1AHr+AHr+AHr+AHr+AHr+AHr+AHr+AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AXv/AXv/A33/BX3/Bn7/CH//CYD/C4D/DYL/D4L/EIP/EoT/FIX/F4f/G4n/HYr/H4v/Ioz/JY7/KJD/K5H/LpL/MZT/NJb/N5f/Opj/PJr/Ppv/QZz/RJ7/SKD/TaL/U6X/Wan/YKz/Zq//arH/bbP/b7T/cbX/crX/c7b/dbf/eLn/e7r/frz/g77/hb//h8D/isL/jsT/kcb/lcj/mcr/nMz/oM7/o8//p9H/qtP/sdb/t9r/vt3/wd//wt//xOD/x+L/zOT/zuX/0Ob/0uf/1en/1+r/2ev/2+z/2+z/3Oz/3e3/3u3/3+7/4O//4u//4/D/5PH/5fH/5/L/6PP/6vT/7fX/7/b/8ff/9Pn/9/r/+Pv/+fv/+/z//P3//P3//P3//P3//f3//f7//v7//v7//v7//v7//v7//v7//v7//v7/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////CP4AlQkcSLCgwYMIEypcyLChw4cQI0qcSLGixYsYM2rcyLGj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6lKb+tSoTrWqV83qVrv61bCOtaxnTeta2/rWuM61rnfN615/JCAAIfkECQMAuQAsAAAAAMgAyACHAAAAAQEBAgICAwMDBAQEBQUFBgYGBwcHCAgICQkJCgoKCwsLDAwMDQ0NDg4ODw8PEBAQEREREhISExMTFBQUFRUVFhYWFxcXGBgYGRkZGhoaGxsbHBwcHR0dHh4eHx8fICAgISEhIiIiIyMjJCQkJSUlJiYmJycnKCgoKSkpKioqKysrLCwsLS0tLi4uLy8vMDAwMTExMjIyMzMzG1WTBXTrAHr8AHr+AHr+AHr+AHr+AHr+AHr+AHr+AHr+AHr+AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AXv/BH3/Bn7/CH//DIH/DYH/DYL/D4P/EYT/FIX/F4b/GYj/G4j/HIn/HYr/H4v/IIv/Ioz/KI//KpH/LZL/MJT/NJX/Opn/Q53/TaL/V6f/XKr/Ya3/ZK7/Z7D/aLH/arH/a7L/bLL/bbP/b7T/crb/drj/ebn/fbv/hL//j8X/l8n/nsz/pND/qdL/rtT/sNb/stf/ttn/utv/vt3/wN7/wt//xOD/xuH/yOL/yeP/zOT/z+b/0ef/1en/2Or/3e3/4O7/4vD/6PL/6vT/7PX/7/b/9Pn/+Pv/+fz/+/3//P3//f3//f3//f3//P3//P3//P3/+/z/+vz/+vv/+fv/+vv/+/z/+/z//P3//f3//f3//f3//f3//f7//v7//v7//v7//v7//v7//v7//v7/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////CP4AcwkcSLCgwYMIEypcyLChw4cQI0qcSLGixYsYM2rcyLGjx48gQ4ocSbKkyZMoU6pcybKly5cwY8qcSbOmzZs4c+rcybOnz59A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RQICACH5BAkEAMMALAAAAADIAMgAhwAAAAEBAQICAgMDAwQEBAUFBQYGBgcHBwgICAkJCQoKCgsLCwwMDA0NDQ4ODg8PDxAQEBERERISEhMTExQUFBUVFRYWFhcXFxgYGBkZGRoaGhsbGxwcHB0dHR4eHh8fHyAgICEhISIiIiMjIyQkJCUlJSYmJicnJygoKCkpKSoqKisrKywsLC0tLS4uLi8vLzAwMDExMTIyMjMzMzQ0NBZcpwB6/QB6/gB6/gB6/gB6/gB6/gB6/gB6/gB7/gB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wB7/wF7/wJ8/wN8/wV9/wZ+/wh//wl//wqA/wyB/w2B/w+C/xGD/xOE/xaG/xuI/x+L/ySN/yaO/yiP/yqQ/yyR/y6S/y+T/zGU/zKU/zSV/zaW/ziY/zqZ/z2a/z+b/0Kd/0Se/0ef/0uh/0+j/1Om/1an/1mp/1yq/2Cs/2Su/2qx/2yy/260/3G1/3W3/3i5/3u6/4C9/4XA/4nC/47E/5HG/5TH/5fJ/53M/6HO/6bQ/6/V/7XY/7rb/7/d/8Lf/8Pf/8Tg/8Xh/8bh/8fi/8ni/8rj/8zk/87l/9Hn/9Pn/9To/9jq/9vs/9/u/+Xx/+by/+fy/+fy/+nz/+r0/+r0/+z1/+72//H3//T5//f6//j7//j7//j7//j6//f6//f6//f6//j7//j7//j7//n7//n7//n8//r8//v8//z9//39//3+//3+//3+//3+//7+//7+//7+//7+/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////wj+AIcJHEiwoMGDCBMqXMiwocOHECNKnEixosWLGDNq3Mixo8ePIEOKHEmypMmTKFOqXMmypcuXMGPKnEmzps2bOHPq3Mmzp8+fQIMKHUq0qNGjSJMqXcq0qdOnUKNKnUq1qtWrWLNOFcWIEB43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Hh4eHx8fICAgISEhIiIiIyMjJCQkJSUlJiYmJycnKCgoKSkpKioqKysrLCwsLS0tLi4uLy8vMDAwKD5VBHTsAHr9AHr+AHr+AHr+AHr+AHr+AHr+AHr+AHr+AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AXv/AXv/Anv/Anz/Anz/Anz/A3z/A3z/BH3/BX3/Bn7/CX//C4D/DYH/DoL/D4L/EYP/EoT/FIX/FYX/Fob/F4b/GIf/Goj/HIn/HYr/H4v/IYz/JI3/J4//KpD/LJL/L5P/MZT/NJX/OJf/O5n/PZr/P5v/QJv/Qpz/RJ3/Rp7/R5//SJ//S6H/TqL/UKT/VKb/WKj/XKr/Yq3/Za//Z7D/abH/bLL/b7T/c7b/d7j/err/f7z/g7//h8H/i8P/jsT/kMX/k8f/lsj/mcr/m8v/nsz/oM3/oc7/os7/pdD/p9H/qtL/rdT/r9X/s9j/t9n/utv/vNz/v93/w9//xuH/zOT/0Ob/1Oj/1+r/2uz/3e3/4O//4/D/5/L/6fP/6/T/7fX/7vb/8Pf/8vj/9fn/9/r/+Pr/+fv/+vv/+vz/+vz/+/z/+/z/+/z/+/z//P3//P3//f3//f3//f3//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////CP4ApwkcSLCgwYMIEypcyLChw4cQI0qcSLGixYsYM2rcyLGjx48gQ4ocSbKkyZMoU6pcybKly5cwY8qcSbOmzZs4c+rcybOnz59AgwodSrSo0aNIkypdyrSp06dQo0qdSrWq1atYs1J9VWqTI0N95LRJA4YHDy5k1LiRk0cQo0uiVmmNWmtUIz9ttpjdy7evX75xClEyNfeoKkyG4PxdzLgxjzmLSBX2GWzUoTSOM2v2KyaQpliTbd76NEjM5tOo91bhs+lWaJirEJVNTZs2GEOlXqsk9omPlNrAgbdx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fP8EfP8Eff8Fff8Hfv8Ifv8Jf/8MgP8Ogv8Qg/8ShP8Uhf8Whv8Yh/8aiP8div8jjP8nj/8pkP8rkf8tkv8wk/8ylf83l/86mf89mv8/nP9Dnf9Gn/9Kof9Mov9Oo/9QpP9RpP9Spf9Wp/9bqv9grP9lr/9psf9us/9ytv93uP98u/+Bvf+GwP+Lwv+OxP+Txv+WyP+ayv+dzP+hzv+m0P+p0v+t1P+v1f+y1/+12P+32f+62/+83P+93P++3f/C3//G4f/I4v/L5P/N5f/Q5v/T6P/W6f/Y6v/b7P/c7P/d7f/e7v/g7v/h7//i8P/j8P/l8f/n8v/o8//p8//q9P/r9P/s9f/v9v/x9//z+P/0+f/1+f/3+v/4+//5+//6/P/7/P/8/f/8/f/9/f/9/f/+/v/+/v/+/v/+/v/+/v/+/v/+/v/+/v/+/v/+/v////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////8I/gCtCRxIsKDBgwgTKlzIsKHDhxAjSpxIsaLFixgzatzIsaPHjyBDihxJsqTJkyhTqlzJsqXLlzBjypxJs6bNmzhz6tzJs6fPn0CDCh1KtKjRo0iTKl3KtKnTp1CjSp1KtarVq1izUl0FSlOlSIwQAdJDx4kTN3Ps6OkjiNEkTaZmaY16KxQlRXvUmN3Lt6/fvWr6ONoka65RYZ4eAXrzt7Hjx3EKTSpl2OcuTIPWPN7M2fEbRZ2EVbbJalIfMZ1Tq/arZhAmXqNfEsu0Z7Xt23zJGKIcO+UtSHBwCx+uB9Ow3iRLISozvPlwOJBuIf+Yqo/z683TOOo1XaOsRNjD/jd3M+l4d4q9HKURz354nU3MzkfUxLi9feF7TMlvaEvQ/f/DGSLdfgcxc0kbACaImxubEFjQLNYpKOFth/zioEARTqihanXwRqArem0oImdiPGKMg5iMqOJmfcBGICIrxvhXHa4QCEwdMua4lxv67WfKf3EIosgjk2DiySmv+GKNL7W0gooomUziCCIZiqgJgZGIZ8cimcBSETKqYMKIHsxNKMl+ylRpmxh5NLKJLh79skkiCCrY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i/8ijP4ljv4qkP4wlP4ylf40lv43l/45mP47mf48mv49mv8/m/5BnP5Enf5Gn/5KoP5Mov5Po/5Tpv5XqP5bqf5frP9hrf5jrv5mr/5osf5rsv5ssv5ss/5ts/5utP5wtf50t/57uv6Dvv6Nw/6Yyf6cy/6hzv6l0P+o0f+r0/+v1f+y1/+12P+52/+72/+83P+/3f/C3//E4P/F4f/I4//M5P/Q5v/U6f/X6v/Z6//c7P/f7v/i8P/m8v/q9P/s9f/w9//y+P/z+P/1+f/3+v/6/P/9/f/9/v/9/v/+/v/+/v/+/v/+/v/+/v/+/v/9/f/8/f/8/f/7/P/7/P/7/P/8/f/9/f/+/v/+/v/+/v/+/v/+/v/+/v////////////////////////////////////////////////////////////////////////////////////////////8I/gDTCRxIsKDBgwgTKlzIsKHDhxAjSpxIsaLFixgzatzIsaPHjyBDihxJsqTJkyhTqlzJsqXLlzBjypxJs6bNmzhz6tzJs6fPn0CDCh1KtKjRo0iTKl3KtKnTp1CjSp1KtarVq1izTj3GS5YrUZkkOVp0SFCWLIUWOZrUKdWrWsGWaZUaDFYpSGfz6t3LV+8fR55e9YI29+ivVpP6Kl7MOEukVLOMFfa5bBYoQ40za+a7aJWvapNzrtpMunTeQ6RyRQtd05fp16UNsULGWmY1RLBza8bTKVdtmKZ0C8/c6FWz3yx5DV/O+A+pYshTcivEvHpfPaqYRT85yrr3vYVg/nHbThLX9/N5G+0iL5KbWfToOUFn/xEUfPh8XtH3aOv+fU3K7LdRNH34B98hvAio0ScGwodHK+MpaBEtDd4HyTESVgTNHhXCRwgwGVLUiWmIOFKJI4d0yJgfuIQo0SyKISLKK7HcAowx2hnUzDHB3OJKJ4uomJcsLkLEjF6EeALLfBQ90wssnrzXoH5FOuSJJq8EM01H0eSCSpAGmgJalTEJ44okd9xnCpk0FQNKHvC5wiZNxoQC53mwzEmnfefVoidNwEjyXR7r/TkTLWBW9weIhsoEjStSLldIMo3OhIyg1VEiTaUycdMKHtW1wulMvqTIXIKjxpQMXpJSmipM/tBwwpymr8JUzWjLUVnrS7CkKVwftO36EozDdSIsTLAs1+KxLrUy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v/6Iwf6Kwv6OxP6Tx/+Zyv6ezP6jz/+p0v+t1P+x1v+02P+52v+93P/B3v/D3//F4P/H4f/J4//M5P/P5v/T6P/W6f/Y6v/b7P/d7f/g7v/i7//j8P/l8f/n8v/q8//r9P/t9f/u9v/v9//x+P/0+f/2+v/4+//4+//5+//5+//6/P/6/P/7/P/7/P/7/f/8/f/9/f/9/v/9/v/+/v/+/v/+/v/+/v/+/v/+/v/+/v////////////////////////////////////////////////////////////////////////////////////////////////////////8I/gDNCRxIsKDBgwgTKlzIsKHDhxAjSpxIsaLFixgzatzIsaPHjyBDihxJsqTJkyhTqlzJsqXLlzBjypxJs6bNmzhz6tzJs6fPn0CDCh1KtKjRo0iTKl3KtKnTp1CjSp1KtarVq1izai3oBdChRIscRbI06hUuZVvTcvXCtq1bL4cqjYpFTK3Wt3jxJgL1Ctg3u1TzCsY7yFOsZoChDl78ls+lV8cSM2VM+S0kVs4kI63MuW0gUpE1E+1M2sseUL9ECy3N2pIt1T9Zy4bkCzZP2bLziFpmOydu3IRebett8/dvRruI0zRufJQ05TGZG280DPpL6cYBxbLeEjvzT9C4/qv0zlxRavEnyTPv8xp9SfXM98xyX9IZsmG/dtV6RYrTo0HwUQYLfS4RA8snhgQomCsEwtTLKoso+FYqDcbkCymFSMgWKRXKVIsmGrLSoUzCiNKHgrKMKFMypRACnx63qCiTM6LkoZ4fvcgoUy+NqDeIMDrGtE0rgJCXCDJBxjSMI+Q9gk2SMEkjCnmlQBmTLH54l4uVMP1yCHaIIMalS8Uwgp0mY77kjCTYvZKmS9JwIt0fybzp0pTMgWJnS998It2We66EjSXMNfJXoCpB8whzbiKqEjNm/kaImI6iVEwgzVWqUi3G5VGXpiiNkimoJ1mzKG59UEoqScUAiNsp/queFIuk0cRqUiS/MWgrSb/8lsiTu44kKm4pBitSM66yhomxI7WCmx7MMBvSM5jK1qi0H5mCWyXYgqTMibLV2a1Hw7Km67gcAYNbJOh6xKRs0ba7kbOy1SLvRsbgVuW9GlUimyT8akRvaXvUGvBFveAG6MEVcVNtaRQybFEmsn0isUWsyPbI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//7+//7+//7+//7+//7+//7+//7+/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////wj+ALsJHEiwoMGDCBMqXMiwocOHECNKnEixosWLGDNq3Mixo8ePIEOKHEmypMmTKFOqXMmypcuXMGPKnEmzps2bOHPq3Mmzp8+fQIMKHUq0qNGjSJMqXcq0qdOnUKNKnUq1qtWrWLNq3cq1q9evYMOKTWrIDqRZY8O6GTIETB9NabuiYkt3yJpBqeJmPVS3LhY+p/RahdO3b5Y+gQVHVVW48WFUip8uakx5y6BakZnOocw5TabMSV9d4czZCh9WoI02Is06TKPURO2wnj1HFWygr7DMnh3m0m2fj3bvtuKH1u+deIQLZ0PqOM5YWpQL5zLJuU1I0pVjeWSdZp7s0g/+dY85Kzp45X/Gv5R0PvseXepZxnoUx0p74XZkxW9pKhCa+7PVkct+Lk3SBoCk7UHgS5bEgSBlgiz4kiayPdgXJBK+hMl/FrKFBSYZujQLIFl0OEQXooTo0igHdmgGLCq2xEsgo1moYIwtWeJFhxjiyJIpa1jYhSk+ykeHhW3AV6RKtXz3ICBLrsQLHw9eMUqUK/XxYB1YZvlgJV2mpEuF950xYJgnxdLifYigiZIqZQD4hStunjRKFwD6UedJ7AHY3J4l7QEgH4CW5IoY92WRV6EjWQIglIyOpGV7YOgXaUiwhHGfIpeKtFp7avDSKUi6BNkeJaOCdMl9eKQK0h3+973iqked3MfdS4nhSNh5ra44RBuDEKkiJbG6ZEhdbQhSSoa8pNHerSzV0Rgbgfy5HyPt9bpSLiVytkYgocQH3XlYyLqSo7upAYgn4+nRXnUr/ZFdGn9s4hyx50GqEhvngWEdGOfJsVIq7eVh3aTZebFSn+C95hwm7YGikiDtLXqcLlycx4hK7oK3xnjSgkdoSvyCp2d3FHuskm7gRTIexOBhIaqd7VnrXC0sZwcZn+dtEd9a4H120rHguREfwtK5fBIg5/URX8rZGYIS0solEl9wJqPkZHZgqpfJeQaftGt2nMR3ynlGn2RqdsKOJ8t5aaCEKHjGxXfeGSgBnB3+FwSaJ10ZKGmanRkEfgEeGXKDpwaBc0snBkpjgEf4fnFmFwZKZIA3BoGRZ/cFSpVL5+9+OSvXBUpngMd3fLCcd/lJzmaHxX6mnLf4SWtKh5p6mpz3BkrJgQeXepWAjZIf5/U4niJNo0TIeRGqR7Vw+paEncjxvXGeeCd9DV4b8W1xHrwmnQ2eFbGMV/t5bZfES3u+dTdJzyqtLV301slbtEpUgmfHeCXLzshQMhnwnM46jNmeSmp1HqEdB1vniR9KdOE36ZzsOFuTzhVgpJKxSWdzzjEceH4XL/gdJxLtCQRLUHieAcIGVg1kiWjO84UzpUYV9gGPF2a2kpCBR3n+oHker1wCQZXBJnbgsVpLVnEfCUZmVe1Zlkvk0J45pMZB5wHfS5jXnk5kpnft4Z5LEjjEyJAJPLt7CRZjKBhQ3CdsMMHaedjAw7QE7zyoikktOnceh6XFe/1SUkyE6DoOiqUWHDpPembyioy1p3pgCcR9sNC+mCCvPVhgV1hGUToB2sQUnZROGtL3FVrYT3aVlImg7uO0r0xPOi6kiSgQRL6tsPA+NqvJK013pa2UwpHtuRFOXCHC9pRhFVoxJYCs0MucIAJBbqibVXQBQ1byJBdqQJAersKLjt1HDObaCRQBNAir9A9AQNxJNdlEFf0ByIo/MQWeEIQ/p/DikgD+0kIqd3I9BLWyKbE4433KKZRVIogPdUSKKgIIIDcIEiiwMIOF5GCbpIgicw8Cg8WEookcImgMIDpKI3ZkIScORZIWwgJBh8IKgT4KKbrIHTSb+RNLNO5BDk1KKm6KoCwEAi09SYU3LaTRpYiimBZCQ0hzUotBzLNDWXCgUjIRSgTdwV432ZCJPNS1pjBsq1elSSiGaiJoOaWAW2VLWF9CCSqmlS1KjIo73+qGQyAzJbJQRDbfyhZIQmWXHcKCWUgpkllIQg8k5esQhEkVlCqWLVqAAyAuYamNyCISenjqY/uQ0Kk887GGccMfKkHYicQiEnkAJmitsFKsFBG0hRH+gxvy4AdDREITGxWIK0qhCUoUog9zSB1s+2LWrPRzuKzRQhrcoAbBIZczXDCpViYhvudaF0BhKBtYQrHX63pXOW7YWVhiQdbvmrcvVwjELvTSiOqe971DIINU48Jd+J73Dq0AjSz+UFX7KlYLcU1NKGTqX77iQYq/4QUj9FbgtKpBur9RhUEbnFFErHc/m1gnhWPWh/xmaBN33LBy+JAiHHkCDx4VMWXCAIjc4igUKFZxYdSgiMp26RSGcEOKKRyHPBYKFYeAQ43ge4U3FGKfhVrFI/TAYOtmwQ6MqOisCJIJQADtsVqQQyAgPOWC1GITi+ADG/prOTwUQhM27HJGREqBiUYEgg9yaMMazCCGeYKhDGtwQx348AdDQAITokijmgdN6EIb+tCITrSiF83oRjv60ZCOtKQnTelKW/rSmM60phMSEAAh+QQJAwDIACwAAAAAyADIAIcAAAABAQECAgIDAwMEBAQFBQUGBgYHBwcICAgJCQkKCgoLCwsMDAwNDQ0ODg4PDw8QEBARERESEhITExMUFBQVFRUWFhYXFxcYGBgZGRkaGhobGxscHBwdHR0eHh4fHx8gICAhISEiIiIjIyMkJCQlJSUmJiYnJycoKCgpKSkqKiorKysYTogFcucAev0Aev4Aev4Aev4Aev4Aev4Aev4Aev4Ae/8Ae/8Ae/8Ae/8Ae/8Ae/8Ae/8Ae/8Ae/8Ae/8Ae/8Ae/8Ae/8Ae/8Ae/8Ae/8Ae/8Ae/8Ae/8Be/8CfP8CfP8DfP8Eff8Fff8Hfv8Hfv8If/8Jf/8KgP8LgP8Mgf8Ngf8Ngv8Ogv8Pgv8Qg/8Rg/8ShP8Thf8Whv8ZiP8bif8div8eiv8gi/8ijP8kjv8nj/8pkP8skf8vk/8wlP8ylf80lf82lv84mP86mf88mf89mv8/m/9BnP9Dnf9Hn/9JoP9Lof9Nov9QpP9Upv9YqP9dq/9krv9psf9ss/9vtP9xtf9ytv91t/94uf98u/+Bvf+Ev/+Iwf+Kwv+Mw/+NxP+OxP+Pxf+VyP+by/+gzf+l0P+o0v+s1P+w1v+12f+72//B3v/D4P/F4f/I4v/L5P/O5f/Q5v/S5//U6P/V6f/X6v/Z6//b7P/d7f/f7v/h7//j8P/m8f/o8v/q9P/v9v/y+P/1+f/4+//6/P/7/P/7/P/8/f/8/f/8/f/8/f/8/f/8/P/7/P/7/P/7/P/8/P/8/f/9/f/9/f/9/v/+/v/+/v/+/v/+/v/+/v/+/v/+/v/+/v////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////8I/gCRCRxIsKDBgwgTKlzIsKHDhxAjSpxIsaLFixgzatzIsaPHjyBDihxJsqTJkyhTqlzJsqXLlzBjypxJs6bNmzhz6tzJs6fPn0CDCh1KtKjRo0iTKl3KtKnTp1CjSp1KtarVq1izat3KtavXr2DDih1LtqzZs2jTql3Ltq3bt3Djyp1Lt67duwY7QcLb1FUZG4Bk8VXax4ZhOKoGH51kuDGYToqJnqrSuPETSZGD9mpTubOizD8LdR5dCDRPTUlGjxZkOucqMKpV9wHW2uad2LH50K4t8xFu3IF4xxT15DduR8JdukJjHHcSS8lZ/mn+e4qn6CkrUTfuJRV2k6m0/mw3jqbVd5KGxjfncxQT2UPqjVcqimrKmVBjK12JH9vLKqJzGDbFI2ONcgZ/quUxFCSd5fFfWK1whmBnmAGVyn6dgaGJWK3QMWFlW3j3022xGTJWHh82psdPkRg3h4hfwaJHioZtyFMrWzTXBXRg9ULih2rsplN62wHyClitMJfiXjqtMoV6aYwCViqwfdiFeTkFwh8VTHoFCoYTlobTKU1MqAcrX11S5oRPoIITHykSAlYiKf5xkyipTegFml/1wsaHUvBJE5wfzhfWKMVNiEhNqzDxIXtjNfKhF7DQhMikgoYFzBsfRjITMFUiaChZoeTJXxozaTehHWgRiuAl/jL9yN91Z5E5oYIwnfLhimlpOWFiL8GHoBKkqOXkhAS+JCF/fbDlK393vITKh1KuNcqETmDJkiMT0uGWhwhWyFIdE07iFmMI8roSK0og6EUvbvXiBYJRuMKSJBOa+Naz8Y2akh8TlgLXJRMGt9IaCKIRlyxYIPjGSq+sGZ+ccbmqXhTwprTJhDbChS+CtKLELX9cCPlWK+3ylyxKFo/X7FxuIOiHSkrG12Vc0/G3RkqtTPgJXQwimFInCDaRsVyhTGgKSqrGp4ZdUSCYCUpBx/cyXXGEi5Kw8TViV8vbeX1SzvFRYhchCLJ2Eor8TV3XyPHtgVLM/OFXV9PqwYHS/hgIAksX0fyRgVIXCJoslyoIcjE4f03cBUviKM0bnxJ4QX6S5PHhZep4il+O4C93Scx55AgeaVei6nVuEubq2VsXMJav3rddiJOM0l/8cWKXKAiOgZIcCJpdVyYOo7QHgsjVRUm6KA2CoJhAI2iwSZIyaxeR8SWCErrxzWEX2/F5ehLx/GFhFxmvomTKhNXK1TOC7Zs0ofhykc+fSmpITxfc6rWhEsD8iQNdjsefmaXEN/zJllwYhqCVneQTExLewCYUspP0ImrWi4vzGHe03yGoC3I5EH/cwJIN8mcTcIEgguy0ko/xR19uASB/zLUSVaQsPoJziyukgCAFsgRc/vzhEVsQyB9WtYR/6sEVW5YVn+SxpHb8SQKM0qLC2bkka89jCwH547+XVI9klaLihw4Bk2lNyIlnCdCEThETciFoC9oqiyU+FK2Y4G1iaKkZf/zlEmCgD0FPYGNZkKgeMHTwJYQcD6TGkgoqjJEmrQATf2A1FlnFhwkPmokgPvSFTH7FhcyrySk2Fx88hGUVOfpQBWcCPgTRryut5I+6agK4CVEBFF6h04eSUCycFOZDYPBbVixBSqs1iTIfYoPrsHKKhn2oCYLMyRfNlJUk0Wh6OZHFHz8EQ6q4AogTmoInczJHGoltKrCwA41scE6exBJB3YTKjGgkB6CoQjw0/gJEVHoxzxRNYWlAWd46Z+MUV1gSWUP5JY3sMM6jqAJh66znUFoRhnXaIAwdO4ooKrrOK7iJKJkoJiDbSRRMOHOdEiSKaCxqgzwIMyjYWyf0iiILcNJoC48wHE9GAVGL0kGnExUhS9uAy54AoxE8ZCkZGlqUU7BunU4gRBxvook0sNQwVLDbUjyBwauKQYg1IcU718lHpdyRpXr4qExU4QfRsRSNTVHEVSsjhT5klCWg6MOT5moYkjolpnxVwyOmWhJYSAIOfK2MX58i18Q2xgp+WCVIXEEJPnDBsY2Z6VSIiFkbkKEPkVDrRlYRiTw4srOG0SdWqoZaw5jBD5J4/ilEZMEJR/QhDY5qrQ2SQEatgFK3hsGCGeawh0AkAhKW+ERiVnEKUGjCEpBABCD0QDfgVkZcWrkEPq3LXRpp4a5aKYUZukteBJGhl19hhU3Ly97YtIGpXPmFCdtL38YogRCmIwskulpf9oaBkmcpBfD6W949ZAotjrACga17BRq25RTrXTBf8xDNtzwiCxJO7BkAfDhAoC7DKfLCI0B3l1P0Ibcg5k8UpBqZUfQzxdRJwh5EGxlR+AGZMI5NFfwgiuSwohG4y3FjzNCIAwsHGJW4g0j7qwQ8lPU8qHAEHdzK3ifYwRGyPU9BSJuHvXYXC3uQBGG1rBDK/oGJjhXDXh4agUIyW0QWnXiEH9jwYTa5QRCTmKKbObIJRxDiD3zIAx3acAYxpDILXyADGtpghz4cIhKXAOieJ03pSlv60pjOtKY3zelOe/rToA61qEdN6lKb+tSoTrWqV82XgAAAIfkECQMAxQAsAAAAAMgAyACHAAAAAQEBAgICAwMDBAQEBQUFBgYGBwcHCAgICQkJCgoKCwsLDAwMDQ0NDg4ODw8PEBAQEREREhISExMTFBQUFRUVFhYWFxcXGBgYGRkZGhoaGxsbHBwcHR0dHh4eHx8fICAgISEhIiIiIyMjJCQkJSUlJiYmJycnKCgoKSkpKioqKysrLCwsLS0tLi4uLy8vMDAwMTExMjIyMzMzNDQ0FV6sA3bxAHr+AHr+AHr+AHr+AHr+AHr+AHr+AHr+AHr+AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AXv/AXv/Anz/A3z/BX3/Bn3/B37/CH//CX//C4D/DYH/DoL/D4L/EYP/EoT/E4X/FYX/F4f/GYf/HIn/H4r/Ioz/JI3/J4//K5H/LZL/MZT/NZb/OJf/O5n/Ppv/Qp3/RJ7/R5//SqH/TKL/TqP/UKT/U6b/Vqf/W6r/Ya3/ZrD/arL/cbX/eLn/fLv/f7z/gb3/hL//hsD/icH/i8P/j8X/lMf/mMn/nMv/ncz/n83/oc7/pdD/qdL/rNT/r9X/stb/tdj/uNr/vNz/v97/w9//x+L/zOT/0Ob/0+j/1+r/2ev/3O3/3u7/4O7/4e//5PD/5vL/6PP/6/T/7PX/7fX/7/b/8Pf/8ff/8vj/8/j/9Pn/9fn/9/r/9/r/+fv/+vz/+/z/+/3//P3//P3//f7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////CP4AiwkcSLCgwYMIEypcyLChw4cQI0qcSLGixYsYM2rcyLGjx48gQ4ocSbKkyZMoU6pcybKly5cwY8qcSbOmzZs4c+rcybOnz59AgwodSrSo0aNIkypdyrSp06dQo0qdSrWq1atYs2rdyrWr169gw4odS7as2bNo06pdy7at27dw48qdS7eu3Y+I2Nxt+goPECB89ioFleYvkCaTBB+l5MXwXzCmFBNFFMWx4TmShfKxbPlQ5p95OFu2oukzzzuiOZ9xZRrnLDupRetpbXNWndipJdGmiRq3aC+jdscU5Dv2G+EvHTUpHnsQcpaWqDCPPQXT85SdukzHXYbVdZOoyP5s943ne8nb43HLMT+SUfrYT/6wFwlKy3vRWSCRxbSmE1E393FWxiZkFWIFEHoJhUiAlqmBylikyOEYIkF5cgWDhrHhnViTgGEZFp8AJSGGQGA2Vh+preeTJCSWCItYnqiBmyI9zSIehm+8GJYj9uHWhSo8FULiGUCCxYpfzMmnUypcYBgGKGFdUsZ2WJCikx4YapFJWIRUkd5sOHkiBYNRJPZVhPdNEeJNvQUoCFiXeBjgHTeNUlmAacwCFinaBdjEJTb5weAUnIjlCIZ21ORKn/c5NxZ690lRCk0LBrhGWaL0eF8gNN343hOllbUIg2PoGRMkDNJ5FhsMPiLTG/4BqonWJAyq+JInDIKJ1ojvNeEJTIQEeMWkaV2y3H2AwNRGgLqmBWl6arxkyhMBFrpWJgxC2VKl7x3X1hoBFuISr+k14pYiAXq7kirSvQeGqWy50th7UaTCknv3NcvWHgEuwhKS75npliYBlrcSGvdtAa9bno6Hxkqt3JleonFhSW8rKlESICNysXgfJSoFS2+RcM3S5HuEqATbe5fOtXJ6qqJkxn150MXteGeoFGAidFUSIMklbRKgdXOxcmx6lqDkcXpX2DXze/qdhO57CdL17HYUnhTIfRTTtfV7yZ4U2nt72DXqezWfNMd94tYVyX10oFTYe67WZcl9LZskJ/7Sdn1y3xgoYXEfsXTBct8Xgd+3F7XpIX6S4OlFsdfhiacnxV2Gv+e4SZCPV8VdrNwHRuXjNW1XKqKTvl0Td5mS+uOD29XJ63rfFypdtL5XBkoIvwdyXWen1wZK5G5XN12DcI0SwOPxXJfFX6L0x31+2HX1dI6adPN2cdcF7nscn/TIfWTY1W56v5t0930byoVJgP6dpEqA6ce1/XYqncE2XWOnl3NKbRpPHegyt/QYDCUiSw8XFuYWVTAuPZ5JicbuI7C3BC89gEpJxPIll5eNhwos6V16zBCXWWzhPlUD4NDgkrv36EtqAUqbW/h1n/CpJBUSGw8W2reWWextPP5PiAxLAHSfCLLlUPdxg0uSd580uAUO4XLJ7AKUtLVwIkBPEMVL9HefAa4FeulJIUtQREW1uEJT6cleS64YICWmhTgB0qKyGKSbs5QCjeNR10vwdZ//mQWM6TneS2Axr/sYwiydyOF4yjcTMt4HRGW53nZSNhNPHO09JhLL+ITFw5gUr1xiGcXJOFiTFt6nC1YCCxT9FL+afHI83fPKITAUy5oYC0Oc6kolzvepDN5Ekquro1Y8Ucj7HPAmnFBkerRgLayo4mkBykJwdMK8+5ihk1OZxSoZpMYwTYFEb2CNVQD5HjLoaCdfw5AczikVOGJImDyREYm8KBUaYiiTPf7ZxDfnyUCmBDBAWtDWT5hIIjlgEymvaREQHDEUVrUIDdNUiiiW1aKYBaUTB2qRGLaUlEl8QaFieBBRLpilChIlEMq8TxPqNxRyMqgPRQFFHBT6F5geZRYUVSgaOPqTWRACjxhqAzuLQgox0BQIVuimTiwhQoWaQaRJuYSXjnoGk9pEFXlIKYO88CumkFShc+BpTR7xQ4VaoRJPIehRo5CHUNDkE2s76l+aIMimpFOuVrhDFV0yiTrw8qhvkspm5OqYNSziFSo5xSCgSVggVI8q/WssELhgh0Yc1COWuMOFJPsXpUalmpK9whwSQbiNdAIRdjAqZw1zSKw4crV/gf6CGe5QiEoMFSKfSMQdxgBbxzSBRlq5X2//kgY65OEPiHjEJCzBieCEwhKQQAQg8kCHNZChc8M1jL+4IgnsZve7JNKCVbOCCdWC97zku51XRtFU9LrXN284xVhaoYdLvve+jtFDP8ESibLi171UcJ4dX/nf7KqBQGtJhHcLDNsrULItm9gmg1frhla+BRJTmnBjtWBEuciCEIzSMImokAc52sUUetiniO8DBTt0VTGboMOK09MEOog1M5i4Q0ZnHJsr4IFowimFIHjLY8uQYRBC/M4sHvEG+074CXCo63w+QQg3jKnAUXDDINY0H4SUIhF02Ox5rTDaVHbZIZQQBGAd/EtYNODBhmemyCcWkYc0XBlDU0ADbW0bZ49sQhKI+MMd3JAGM4zhC5DTQhjKkIY2yKEOeNADIVja50pb+tKYzrSmN83pTnv606AOtahHTepSm/rUqE61qlfN6lZzJSAAIfkECQQA1AAsAAAAAMgAyACHAAAAAQEBAgICAwMDBAQEBQUFBgYGBwcHCAgICQkJCgoKCwsLDAwMDQ0NDg4ODw8PEBAQEREREhISExMTFBQUFRUVFhYWFxcXGBgYGRkZGhoaGxsbHBwcHR0dHh4eHx8fICAgISEhIiIiIyMjJCQkJSUlJiYmJycnKCgoKSkpKioqKysrLCwsLS0tLi4uLy8vMDAwMTExMjIyMzMzNDQ0NTU1NjY2Nzc3ODg4OTk5DG3UAXj4AHr+AHr+AHr+AHr+AHr+AHr+AHr+AHr+AHr+AHr+AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AHv/AXv/Anz/BH3/Bn7/CH//CX//CoD/DIH/DYH/DoL/EIP/EoT/E4T/FIX/Fob/F4f/GYj/G4n/Hor/H4v/IYz/JI3/J4//KJD/KpD/K5H/LZL/MpT/Npf/O5n/P5v/Qpz/RZ7/SaD/TKL/TqP/U6X/V6f/W6n/X6z/Yq3/Zq//aLH/a7L/bbP/brT/cbX/dLf/eLn/e7r/f7z/h8D/i8P/j8T/k8b/l8j/nMv/oc7/pM//qNH/q9P/rdT/sNb/s9f/tdn/uNr/utv/u9z/vt3/wd7/w9//xeH/x+L/yuP/zeX/0ef/1Oj/1+r/2ev/3Oz/3+7/4e//4/D/5fH/6PP/6vT/7PT/7PX/7fX/7vb/7/b/8Pf/8vj/9Pn/9vr/9vr/9/r/+Pv/+fv/+fv/+vz/+/z/+/z/+/z/+/z//Pz//P3//f3//f3//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7//v7/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////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AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA==" />
                        </div>
                    </div>
                    <p class="mb-0 mt-2 disable-select" style="font-size: 1.25rem;" unselectable="on"><strong>Please Wait</strong></p>
                </div>
            </div>
        </div>
        <!-- The attendance status snackbar -->
        <div id="snackbar"></div>
        <!-- Main Body -->
    	<div class="container-fluid px-2 pt-2">
    		<!-- Title Tab -->
            <div class="w-100 p-3 shadow-sm rounded bg-light text-dark">
                <input type="hidden" id="current-event-ID" value="<?php echo $eventID;?>">
                <h1 class="font-weight-bold header-title" id="current-event-title"><?php echo $eventTitle;?></h1>
                <h2 class="pl-3 font-weight-normal">Scan Attendance and Generate Certificate Immediately.</h2>
            </div>
            <!-- Go Back to Events Button -->
            <div class="mt-3">
                <a href="events.php" class="ml-3 h4 btn btn-secondary btn-lg-add-event rounded-pill"><i class="fas fa-arrow-circle-left"></i> GO BACK</a>
            </div>
            <?php 
                // Check if the fetch is successful
                if ($scanAttendanceErrorFlag == true) { ?>
                    <!-- Barcode Reader -->
                    <!-- <div id="qr-reader" class="container container-scan-barcode bg-white my-3" style="border: 10px solid #929eaa;"></div> -->
                    <div class="mx-auto text-center">
                        <button class="btn btn-success btn-lg-add-event" id="scanAttendanceBtn" data-toggle="modal" data-target="#scanAttendanceModal"><i class="fas fa-check"></i> CLICK HERE TO SCAN ATTENDANCE</button>
                    </div>
                    <!-- Toggle Between Attendance and Invitee List Buttons -->
                    <div class="container mt-2 mb-2 p-2 border-form-override event-invitee-parent">
                        <div class="row">
                            <!-- Event Details Button -->
                            <div class="col-sm-6 mt-1 d-flex justify-content-center" id="headingOne">
                                <button class="btn btn-success btn-lg-add-event btn-lg-event-invitee" type="button" data-toggle="collapse" data-target="#collapseAttendanceList" aria-expanded="true" aria-controls="collapseOne" onclick="buttonFlagFunc(0)"><i class="fas fa-clock"></i> ATTENDANCE LIST</button>
                            </div>
                            <!-- Invitees' Details Button -->
                            <div class="col-sm-6 mt-1 d-flex justify-content-center" id="headingTwo">
                                <button class="btn btn-primary btn-lg-add-event btn-lg-event-invitee" type="button" data-toggle="collapse" data-target="#collapseInviteesList" aria-expanded="false" aria-controls="collapseTwo" onclick="buttonFlagFunc(1)"><i class="fas fa-users"></i> INVITEES LIST</button>
                            </div>
                        </div>
                    </div>
                    <!-- Attendance and Invitees List Accordion -->
                    <div class="container shadow-sm mt-2 mb-4 p-3 border-form-override event-invitee-parent">
                        <h6 id="accordionInfo"><i class="fas fa-info-circle"></i> Please click Attendance or Invitees List Button above to display the table.</h6>
                        <div class="accordion" id="accordionAttendance">
                            <!-- Attendance Part -->
                            <div class="event-attendance-details">
                                <div id="collapseAttendanceList" class="collapse" aria-labelledby="headingOne" data-parent="#accordionAttendance">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="attendanceList" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th id="attendanceNum">No.</th>
                                                        <th id="attendanceName">Name</th>
                                                        <th id="attendanceInviteeCode">Invitee Code</th>
                                                        <th id="attendanceType">Type</th>
                                                        <th id="attendanceDateTime">Date and Time</th>
                                                        <th id="attendanceSend">
                                                            <button class="btn btn-info btn-lg-add-invitee" id="sendSelectedCertificates" style="display: none; margin-right: 0.5rem;"><i class="fas fa-envelope"></i> SEND</button>
                                                            <input type="checkbox" id="selectAllInvitees">
                                                        </th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm">
                                                <h5><i class="fas fa-user-check"></i> Attendance Checked: <span class="totalPresent">N</span>/<span class="totalInvitees">N</span></h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Invitees List Part -->
                            <div class="event-attendance-details">
                                <div id="collapseInviteesList" class="collapse" aria-labelledby="headingOne" data-parent="#accordionAttendance">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="inviteeList" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th id="inviteeNum">No.</th>
                                                        <th id="inviteeStatus">Status</th>
                                                        <th id="inviteeName">Name (Type)</th>
                                                        <th id="inviteeCode">Invitee Code</th>
                                                        <th id="inviteeEmail">Email</th>
                                                        <th id="inviteePhoneNum">Phone No.</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-sm mb-2">
                                                <h5><i class="fas fa-check-circle"></i> Present: <span class="totalPresent">N</span></h5>
                                            </div>
                                            <div class="col-sm mb-2">
                                                <h5><i class="fas fa-times-circle"></i> Absent: <span id="totalAbsent">N</span></h5>
                                            </div>
                                            <div class="col-sm">
                                                <h5><i class="fas fa-users"></i> Total: <span class="totalInvitees">N</span></h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- The certificate options modal -->
                    <div  id="certOptionsModal" class="modal" data-backdrop="static" data-keyboard="false" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 650px;">
                            <div class="modal-content border-form-override">
                                <div class="modal-header bg-primary add-edit-invitee-override">
                                    <h5 class="modal-title text-light add-edit-invitee-title" id="exampleAddEditInvitee"><i class="fas fa-cog"></i> Options</h5>
                                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form method="post" id="certificateOptionForm" onsubmit="return optionConfigInputValidation()">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="certLayout" class="label-add-edit-event">Attendance Config:</label>
                                                    <div class="p-2 time-event">
                                                        <div class="form-group">
                                                            <label class="label-add-edit-event">Generate Certificate:</label>
                                                            <div class="custom-control custom-radio custom-control-inline ml-1">
                                                                <input type="radio" id="generateCertAuto" name="generateCert" class="custom-control-input" value="Auto" checked>
                                                                <label class="custom-control-label" for="generateCertAuto">Automatic</label>
                                                            </div>
                                                            <div class="custom-control custom-radio custom-control-inline">
                                                                <input type="radio" id="generateCertManual" name="generateCert" class="custom-control-input" value="Manual">
                                                                <label class="custom-control-label" for="generateCertManual">Manual</label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="label-add-edit-event">Send Certificate After Attendance:</label>
                                                            <div class="custom-control custom-switch ml-1">
                                                                <input type="checkbox" class="custom-control-input" id="sendCert" name="sendCert">
                                                                <label class="custom-control-label" for="sendCert" id="sendCertLabel">No</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="certLayout" class="label-add-edit-event">Certificate Layout:</label>
                                                    <div class="p-2 time-event">
                                                        <div class="form-group">
                                                            <label for="certOrientation" class="label-add-edit-event">
                                                                Orientation:
                                                            </label>
                                                            <select class="form-control" name="certOrientation" id="certOrientation">
                                                                <option value='L' selected>Landscape</option>
                                                                <option value='P'>Portrait</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="certSize" class="label-add-edit-event">
                                                                Size:
                                                            </label>
                                                            <select class="form-control" name="certSize" id="certSize">
                                                                <option value='Letter' selected>Letter</option>
                                                                <option value='A4'>A4</option>
                                                                <option value='A3'>A3</option>
                                                                <option value='A5'>A5</option>
                                                                <option value='Legal'>Legal</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="certBarcodePosition" class="label-add-edit-event">
                                                        Barcode Position:
                                                    </label>
                                                    <div class="form-group">
                                                        <label for="certBarcodePositionX">X:</label>
                                                        <input type="number" class="form-control form-control-custom" id="certBarcodePositionX" name="certBarcodePositionX" min="0" max="300" value="20">
                                                        <label for="certBarcodePositionY">Y:</label>
                                                        <input type="number" class="form-control form-control-custom" id="certBarcodePositionY" name="certBarcodePositionY" min="0" max="300" value="169">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="certLayout" class="label-add-edit-event">Certificate Text Style:</label>
                                                    <div class="p-2 time-event">
                                                        <div class="form-group">
                                                            <label for="certFont" class="label-add-edit-event">
                                                                Font:
                                                            </label>
                                                            <select class="form-control" name="certFont" id="certFont">
                                                                <option value='Helvetica' selected>Helvetica</option>
                                                                <option value='Courier'>Courier</option>
                                                                <option value='Times'>Times</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="certFontStyle" class="label-add-edit-event">
                                                                Style:
                                                            </label>
                                                            <select class="form-control" name="certFontStyle" id="certFontStyle">
                                                                <option value='' selected>Regular</option>
                                                                <option value='B'>Bold</option>
                                                                <option value='I'>Italic</option>
                                                                <option value='U'>Underline</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="certFontSize" class="label-add-edit-event">
                                                                Size (8-72):
                                                            </label>
                                                            <input type="number" class="form-control form-control-custom" id="certFontSize" name="certFontSize" min="8" max="72" value="30">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="certFontColor" class="label-add-edit-event">
                                                                Color:
                                                            </label>
                                                            <input type="color" class="form-control form-control-custom" id="certFontColor" name="certFontColor" value="#000000" style="padding: 0.1rem;">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="certTextPosition" class="label-add-edit-event">
                                                                Position:
                                                            </label>
                                                            <div class="form-group">
                                                                <label for="certTextPositionX">X:</label>
                                                                <input type="number" class="form-control form-control-custom" id="certTextPositionX" name="certTextPositionX" min="0" max="300" value="130">
                                                                <label for="certTextPositionY">Y:</label>
                                                                <input type="number" class="form-control form-control-custom" id="certTextPositionY" name="certTextPositionY" min="0" max="300" value="79">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                                        <button type="button" class="btn btn-primary" id="previewCertificateBtn"><i class="fa fa-eye"></i> Preview</button>
                                        <button type="submit" class="btn btn-success" name="certificateOptionSave" value="Save" id="certificateOptionSave"><i class="fas fa-save"></i> Save</button>
                                    </div>
                                </form>                 
                            </div>
                        </div>
                    </div>
                    <!-- Preview Certificate Config Modal -->
                    <div class="modal" id="previewCertConfigModal" tabindex="-1" role="dialog" aria-labelledby="viewModalTitle" aria-hidden="true" data-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h4 class="modal-title text-light" id="viewCertificateModalTitleName">Preview Certificate</h5>
                                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <iframe id="previewCertFile" class="embed-responsive-item" src="" allowfullscreen></iframe>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Scan Attendance Modal -->
                    <div class="modal" id="scanAttendanceModal" tabindex="-1" role="dialog" aria-labelledby="viewModalTitle" aria-hidden="true" data-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content container-scan-barcode" id="scanAttendanceContent" style="border: 10px solid #929eaa;">
                                <div class="modal-header bg-primary add-edit-invitee-override">
                                    <h4 class="modal-title text-light"><?php echo $eventTitle;?> | Scan Attendance</h5>
                                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <!-- Camera Mode Content -->
                                    <div id="cameraModeContent">
                                        <!-- Permission Status -->
                                        <div id="permissionStatus" class="text-center" style="font-weight: 700; color: #ff0000;">
                                            <p><i class="fas fa-info-circle"></i> Please approve permission first.</p>
                                        </div>
                                        <!-- QR Code Reader Content -->
                                        <div id="qr-reader-content" style="display: none;">
                                            <div id="qr-reader" class="mb-2" style="max-width: 500px; margin: auto;"></div>
                                            <div id="qrButtonControls" class="text-center">
                                                <div id="qrStartSelection">
                                                    <select id="cameraSelection" class="form-control col-sm-6 mx-auto mb-1">
                                                        <!-- Camera Selection -->
                                                    </select>
                                                    <button id="startQRCodeScanner" class="btn btn-success mb-1"><i class="fas fa-play"></i> Start Scanning</button>
                                                </div>
                                                <button id="stopQRCodeScanner" class="btn btn-danger mb-1" style="display: none;"><i class="fas fa-stop"></i> Stop Scanning</button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Code Input  Content-->
                                    <div id="codeInputModeContent" style="display: none;">
                                        <form id="codeInputModeForm">
                                            <div class="form-group text-center container">
                                                <label for="inviteeCodeInput" class="label-add-edit-event">Please Enter Invitee Code <span id="requiredCodeInputError"></span></label>
                                                <input type="text" class="form-control mx-auto mb-2" id="inviteeCodeInput" name="inviteeCodeInput" style="max-width: 400px">
                                                <button type="submit" class="btn btn-success"><i class="fas fa-sign-in-alt"></i> Enter</button>
                                                <small class="form-text">You can use barcode scanner which is optional.</small>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- Scan Selection Mode -->
                                    <div class="form-group text-center mb-2">
                                        <label for="scanSelectionMode">Scan Mode: </label>
                                        <select id="scanSelectionMode" class="form-control col-sm-6 mx-auto">
                                            <option value="cameraMode">Camera</option>
                                            <option value="codeInputMode">Code Input</option>
                                        </select>
                                    </div>
                                    <!-- Scanned Result Message -->
                                    <div class="p-3 text-light rounded" id="scannedResultMsg" style="display: none">
                                        <p class="font-weight-bolder mb-0">RESULT: <span class="float-right"><a href="javascript:void(0)" class="text-light" id="closeScannedResult" data-toggle="tooltip" title="Close Message" ><i class="fas fa-times"></i></a></span></p>
                                        <p class="mb-0" id="scannedInviteeName"></p>
                                        <p class="mb-0">Code: <span id="scannedInviteeCode"></span></p>
                                        <p class="font-weight-bold mb-0">Status: <span id="scannedInviteeResult"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php 
                } else { ?>
                    <!-- If fetched error, then the error message appear. -->
                    <div class="w-100 p-3 mt-3 mb-5 shadow-sm rounded bg-danger text-light response">
                        <h4>Fetch Error, Please try again.</h4>
                    </div>
            <?php } ?>
            
    	</div>
    </div>
    <?php 
        include 'model/footer.php';
        // the include or require statement takes all the text/code/markup that exists in the specified file    
    ?>
    <!-- Barcode Scanner Script -->
    <script src="dist/html5-qrcode.min.js"></script>
    <!-- Attendance AJAX Script -->
    <script src="scripts/attendance-ajax.js"></script>
    <script>
        var attendanceListBtnFlag = true;
        var inviteesListBtnFlag = true;

        // Get Current Event ID
        var eventID = document.getElementById('current-event-ID').value;

        // Get Attendance Status Snackbar
        var statusSnackBar = document.getElementById("snackbar");

        // Get Barcode Reader
        var barcodeReader = document.getElementById("scanAttendanceContent");

        function buttonFlagFunc(flag) {
            if (flag == 1) {
                if (attendanceListBtnFlag == true) {
                    document.getElementById("accordionInfo").style.display = "none";
                    attendanceListBtnFlag = false;
                    inviteesListBtnFlag = true;
                }else{
                    document.getElementById("accordionInfo").style.display = "initial";
                    attendanceListBtnFlag = true;
                    inviteesListBtnFlag = true;
                }
            }else{
                if (inviteesListBtnFlag == true) {
                    document.getElementById("accordionInfo").style.display = "none";
                    inviteesListBtnFlag = false;
                    attendanceListBtnFlag = true;
                }else{
                    document.getElementById("accordionInfo").style.display = "initial";
                    inviteesListBtnFlag = true;
                    attendanceListBtnFlag = true;
                }
            }
        }

        // Change Between Auto and Manual for generating certificate
        $("input[name='generateCert']").click(function() {
            if ($("#generateCertManual").is(":checked")) {
                $("input[name='sendCert']").prop({
                    'checked': false,
                    'disabled': true
                });
                $("#sendCertLabel").html("No");
            } else {
                $("input[name='sendCert']").prop('disabled', false);
            }
        });

        // Change Send Certificate Label
        $("input[name='sendCert']").click(function() {
            if ($(this).is(":checked")) {
                $("#sendCertLabel").html("Yes");
            } else {
                $("#sendCertLabel").html("No");
            }
        });

        // Option Config Input Validation
        function optionConfigInputValidation(){
            var valid = true;

            var certBarcodePositionX = $("#certBarcodePositionX").val();
            var certBarcodePositionY = $("#certBarcodePositionY").val();
            var certFontSize = $("#certFontSize").val();
            var certTextPositionX = $("#certTextPositionX").val();
            var certTextPositionY = $("#certTextPositionY").val();

            if(certBarcodePositionX.trim() == ""){
                $("#certBarcodePositionX").val("0");
                valid = false;
            }
            if(certBarcodePositionY.trim() == ""){
                $("#certBarcodePositionY").val("0");
                valid = false;
            }
            if(certFontSize.trim() == ""){
               $("#certFontSize").val("8");
                valid = false;
            }
            if(certTextPositionX.trim() == ""){
                $("#certTextPositionX").val("0");
                valid = false;
            }
            if(certTextPositionY.trim() == ""){
                $("#certTextPositionY").val("0");
                valid = false;
            }
            return valid;
        }
    </script>
</body>
</html>