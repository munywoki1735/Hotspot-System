// scripts.js
function showPaymentForm(package) {
    // Open the modal
    document.getElementById("payment-modal").style.display = "flex";
    
    // You can use the package variable to show information related to the selected package if needed
    console.log(package);  // For now, just log the selected package to the console
}

function closePaymentForm() {
    // Close the modal
    document.getElementById("payment-modal").style.display = "none";
}

document.getElementById("payment-form").addEventListener("submit", function(e) {
    e.preventDefault();  // Prevent form submission
    
    const mpesaNumber = document.getElementById("mpesa-number").value;
    
    // Validate the M-Pesa number (Kenyan numbers start with 07 or 01 and are 10 digits long)
    const mpesaRegex = /^(07|01)[0-9]{8}$/;
    
    if (!mpesaRegex.test(mpesaNumber)) {
        alert("Please enter a valid Kenyan M-Pesa phone number.");
    } else {
        // Proceed with the payment process (trigger M-Pesa API here)
        alert("Payment successful! Proceeding with the connection...");
        closePaymentForm();
    }
});
