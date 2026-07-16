const quoteForm = document.querySelector("#quote-form");
const formStatus = document.querySelector("#form-status");

quoteForm?.addEventListener("submit", (event) => {
  event.preventDefault();
  if (!quoteForm.reportValidity()) return;
  const data = new FormData(quoteForm);
  const message = [
    "Hello Le Duong Cashew, I would like to request a quotation.", "",
    `Name: ${data.get("name")}`, `Company: ${data.get("company")}`,
    `Email: ${data.get("email")}`, `Product / grade: ${data.get("product")}`,
    `Quantity: ${data.get("quantity")}`, `Destination: ${data.get("destination")}`,
    `Specification / notes: ${data.get("notes") || "Please advise"}`,
  ].join("\n");
  window.open(`https://wa.me/84886106677?text=${encodeURIComponent(message)}`, "_blank", "noopener,noreferrer");
  formStatus.textContent = "Your quotation request is ready in WhatsApp.";
});
