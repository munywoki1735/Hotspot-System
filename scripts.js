// Store the selected package name, price, and package id
let selectedPackage = "";
let selectedPrice = 0;
let selectedPackageId = 0; // New variable for package ID

// Function to show the payment modal with the selected package
function selectPackage(packageName, packagePrice, packageId) {
    selectedPackage = packageName; // Set the selected package name
    selectedPrice = packagePrice; // Set the selected price
    selectedPackageId = packageId; // Set the selected package ID

    // Set the selected package name in the confirmation modal
    document.getElementById("selected-package").textContent = packageName;

    // Show the payment modal
    document.getElementById("confirmation-modal").style.display = "flex";

    // Set hidden form values with the selected package name, price, and id
    document.getElementById("package-name").value = packageName;
    document.getElementById("package-price").value = packagePrice;
    document.getElementById("package-id").value = packageId; // Set the hidden package id
}

// Function to close the payment form/modal
function closePaymentForm() {
    document.getElementById("confirmation-modal").style.display = "none";
}

// Handle payment form submission
document.getElementById("payment-form").addEventListener("submit", function(e) {
    e.preventDefault();  // Prevent form submission

    const mpesaNumber = document.getElementById("mpesa-number").value;

    // Validate the M-Pesa number (Kenyan numbers start with 07 or 01 and are 10 digits long)
    const mpesaRegex = /^(07|01)[0-9]{8}$/;

    if (!mpesaRegex.test(mpesaNumber)) {
        alert("Please enter a valid Kenyan M-Pesa phone number.");
    } else {
        // If the number is valid, trigger the form submission manually
        alert("Payment successful! Proceeding with the connection...");
        
        // Submit the form after successful validation
        document.getElementById("payment-form").submit();  // This line triggers the form submission
    }
});
