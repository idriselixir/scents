"use strict";

// Client-only behaviour for the storefront: native <dialog> control and focus.
// Everything else (filters, search, compare, bag) lives in the Storefront Livewire component.

const focusSearch = () => document.querySelector("#search")?.focus();

document.addEventListener("click", (event) => {
  const opener = event.target.closest("[data-open-dialog]");
  if (opener) document.getElementById(opener.dataset.openDialog)?.showModal();

  if (event.target.closest("[data-browse]")) {
    document.querySelector("#bag-dialog").close();
    document.querySelector("#collection").scrollIntoView();
    document.querySelector('#filters [aria-pressed="true"]')?.focus({ preventScroll: true });
  }

  // A click on the backdrop lands on the dialog itself, outside its box.
  if (event.target instanceof HTMLDialogElement) {
    const bounds = event.target.getBoundingClientRect();
    const outside = event.clientX < bounds.left || event.clientX > bounds.right
      || event.clientY < bounds.top || event.clientY > bounds.bottom;
    if (outside) event.target.close();
  }
});

document.addEventListener("livewire:init", () => {
  // Wait a frame so the dialog's new contents are on the page before it opens.
  Livewire.on("open-dialog", ({ id }) => requestAnimationFrame(() => {
    const dialog = document.getElementById(id);
    if (dialog && !dialog.open) dialog.showModal();
  }));
  Livewire.on("close-dialog", ({ id }) => document.getElementById(id)?.close());
  Livewire.on("focus-search", () => requestAnimationFrame(focusSearch));
  Livewire.on("bag-row-removed", () => requestAnimationFrame(() => {
    document.querySelector("#bag-contents [data-quantity]:not([disabled]), #bag-contents [data-browse]")?.focus();
  }));
});
