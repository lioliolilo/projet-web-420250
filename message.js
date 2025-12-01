document.getElementById("contactForm").addEventListener("submit", function (e) {
    e.preventDefault(); // STOP page reload

    const payload = {
        id_tuteur: document.getElementById("tuteur").value,
        nom: document.getElementById("nom").value,
        email: document.getElementById("email").value,
        sujet: document.getElementById("sujet").value,
        message: document.getElementById("message").value
    };

    fetch("api/send_message.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(res => {
        const msg = document.getElementById("formResult");

        if (res.success) {
            msg.style.color = "green";
            msg.textContent = "Votre message a été envoyé !";
            msg.classList.add("show");   // fade-in

            document.getElementById("contactForm").reset();
        } else {
            msg.style.color = "red";
            msg.textContent = "Erreur : " + res.error;
            msg.classList.add("show");
        }
    })
    .catch(() => {
        const msg = document.getElementById("formResult");
        msg.style.color = "red";
        msg.textContent = "Erreur serveur.";
        msg.classList.add("show");
    });
});
