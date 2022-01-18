<?php require_once('../fragments/header.php'); ?>

    <div class="modal fade" id="addCardModal" tabindex="-1" role="dialog" aria-labelledby="addcard"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Dodaj nową kartę!</h5>
                    <button type="button" class="close btn btn-danger" data-dismiss="modal" aria-label="Close"
                            onclick="$('#addCardModal').modal('hide')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row mx-auto">
                            <input class="form-check" id="cardNr" placeholder="Numer karty">
                        </div>
                        <div class="row mx-auto">
                            <input class="me-2  col form-check" id="expirationDate" placeholder="MM/YY" maxlength="5">
                            <input class="ms-2 col form-check" id="cvv" placeholder="CVV" maxlength="4">
                        </div>
                        <input class="mx-auto btn btn-primary" value="dodaj">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <main class="align-self-center mx-auto">
            <div class="row g-5 mt-auto justify-content-center">
                <div class="col-8 flex-row-reverse">
                    <div class="row">
                        <form class="col-8" action="#">
                            <div class="row my-2">
                                <h3 class="col-8">Sposób płatności</h3>
                                <button class="col-4 btn btn-primary" onclick="return add_card()">Dodaj kartę</button>
                            </div>
                            <ul id='cards' class="row list-group ">
                            </ul>
                        </form>
                    </div>

                </div>
                <div class="col-4 ">
                    <div class="row  border border-primary p-2">
                        <h4 class="text-center">Podsumowanie Koszyka</h4>
                        <h4 id="basket" class="text-center">Koszyk: </h4>
                        <h4 id="delivery_sum" class="text-center">Dostawa: </h4>
                        <h2 id="sum" class="text-center">RAZEM: </h2>
                        <button class="my-2 btn btn-primary text-center">Wróć</button>
                    </div>
                    <h2 id="suma" class="col align-self-end my-2">
                    </h2>
                </div>

            </div>
            <div class="row">
                <button id="cancel" type="reset" class="btn btn-danger col-4 my-3 me-auto"
                        onclick="window.location.href='../index.php'"> Wyjdz
                </button>
                <button id="accept" type="submit" class="btn btn-primary ms-auto col-4 my-3 align-self-end"
                        onclick="return finalize();">Zaakceptuj
                </button>
            </div>
        </main>
    </div>

    <script>

        function finalize() {
            Cookies.set("payment_method", JSON.stringify(window.payment_method))
            window.location.href = "summary.php"
        }

        function selected(id) {
            window.payment_method = window.payments.find(item => item["id"] === id)
        }

        function add_card() {
            $('#addCardModal').modal("show")
        }

        $(function () {
            window.delivery = JSON.parse(Cookies.get("delivery"))
            $("#basket").text("Koszyk: " + currencyFormatter.format(Cookies.get("basket_sum")))
            $("#delivery_sum").text("Dostawa: " + currencyFormatter.format(window.delivery["price"]))
            $("#sum").text("RAZEM: " + currencyFormatter.format(window.delivery["price"] + parseInt(Cookies.get("basket_sum"))))
            $.ajax(ip_address + "/payments/" + user_id).done(function (data) {
                window.payments = data["payments"]

                let first = true
                for (let key in data["payments"]) {
                    const entry = data['payments'][key]
                    let liel = document.createElement("li")
                    let name = entry["name"]
                    if (entry["type"] === "card") {
                        name = "Karta o ostatnich cyfrach " + entry["four-digits"]
                    }
                    let checked = ""
                    if (first) {
                        checked = "checked"
                        first = false
                    }

                    liel.className = "list-group-item border-primary border-top"
                    liel.innerHTML = `<input id="item_${entry['id']}" name="delivery_form" class="form-check-input" type="radio" onclick="return selected(${entry['id']});" value="" ${checked}>
                                    <label for="item_${entry['id']}" class="form-check-label" >${name}</label>`
                    $("#cards").append(liel)
                }
                selected(window.payments[0]["id"])
            })
        })
    </script>
<?php require_once("../fragments/footer.php"); ?>