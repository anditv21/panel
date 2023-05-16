let capsLockOn = false; // Tracks the state of the Caps Lock key

document.addEventListener('keydown', function(event) {
  if (event.getModifierState && event.getModifierState('CapsLock')) {
    capsLockOn = true;
    updateCapsLockMessage();
  }
});

document.addEventListener('keyup', function(event) {
  if (event.getModifierState && !event.getModifierState('CapsLock')) {
    capsLockOn = false;
    updateCapsLockMessage();
  }
});

function updateCapsLockMessage() {
  var message = document.getElementById('caps-lock-message');
  if (capsLockOn) {
    message.style.display = 'block';
  } else {
    message.style.display = 'none';
  }
}

var throttledUpdateCapsLockMessage = throttle(updateCapsLockMessage, 200);

var passwordInputs = document.querySelectorAll('input[type="password"]');
passwordInputs.forEach(function(input) {
  input.addEventListener('input', throttledUpdateCapsLockMessage);
});
