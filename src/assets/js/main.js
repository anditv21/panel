getBrowser();

$(document).ready(function () {
  $('[data-toggle="tooltip"]').tooltip();
});

function setClipboard(value) {
  var tempInput = document.createElement("input");
  tempInput.style = "position: absolute; left: -1000px; top: -1000px";
  tempInput.value = value;
  document.body.appendChild(tempInput);
  tempInput.select();
  document.execCommand("copy");
  document.body.removeChild(tempInput);
}



document.addEventListener("DOMContentLoaded", function() {
  var capsLockMessage = document.getElementById("caps-lock-message");

  document.querySelectorAll("input[type='password']").forEach(function(input) {
      input.addEventListener("keyup", function(event) {
          if (event.getModifierState && event.getModifierState("CapsLock")) {
            capsLockMessage.style.color = "red";
            console.log("Caps Lock is on");
          } else {
            capsLockMessage.style.color = "#0d0f0f";
            console.log("Caps Lock is off");
          }
      });
  });
});




function copyToClipboard(text) {
  const textarea = document.createElement("textarea");
  textarea.value = text;
  document.body.appendChild(textarea);
  textarea.select();
  document.execCommand("copy");
  document.body.removeChild(textarea);
}

function bulkDownload(tableElement) {
  let txtContent = "";
  const rows = Array.from(tableElement.getElementsByTagName("tr"));

  rows.forEach((row) => {
    const codeCell = row.querySelector("p");
    if (codeCell) {
      const code = codeCell.innerText.trim();
      txtContent += code + "\n";
    }
  });

  const blob = new Blob([txtContent], { type: "text/plain" });
  const anchor = document.createElement("a");
  anchor.href = URL.createObjectURL(blob);
  anchor.download = "codes.txt";
  anchor.click();
}

function getBrowser() {
  let browserName = "Unknown";

  if (!!navigator.brave && !!navigator.brave.isBrave()) {
    browserName = "Brave";
  } else if (
    /Opera|OPR\//i.test(navigator.userAgent) ||
    navigator.vendor.includes("Opera")
  ) {
    browserName = "Opera";
  } else if (!!navigator.vendor && navigator.vendor.includes("Apple")) {
    browserName = "Safari";
  } else if (!!window.safari) {
    browserName = "Safari";
  } else if (/Firefox/i.test(navigator.userAgent)) {
    browserName = "Firefox";
  } else if (/Edg/i.test(navigator.userAgent)) {
    browserName = "Microsoft Edge";
  } else if (!!window.chrome) {
    browserName = "Chrome";
  }

  const expirationDate = new Date();
  expirationDate.setFullYear(expirationDate.getFullYear() + 1);

  document.cookie = `browser=${browserName}; expires=${expirationDate.toUTCString()}; path=/`;
}
