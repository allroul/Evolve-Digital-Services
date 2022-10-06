$(document).ready(function () {
  /*Clients PopUps*/
  const clientsSeeMoreBtns = document.querySelectorAll('.clients-see-more-btn');
  const popUpCrosses = document.querySelectorAll('.pop-up-content svg');
  const popUps = document.querySelectorAll('.client-pop-up');
  let body = document.querySelector('body'),
    timeOut = 800;

  for (clientsSeeMoreBtn of clientsSeeMoreBtns) {
    clientsSeeMoreBtn.addEventListener('click', (e) => {
      e.target.closest('div').querySelector('.client-pop-up').style.overflowY = 'auto';
      e.target.closest('div').querySelector('.client-pop-up').classList.add('_open');
      e.target.closest('div').querySelector('.pop-up-content').classList.add('_open');
      bodyLock();
      body.classList.add('lock');
      e.stopPropagation();
      closeOnOverlay();
    });
  }

  for (popUpCross of popUpCrosses) {
    popUpCross.addEventListener('click', (e) => {
      e.target.closest('.client-pop-up').style.overflowY = 'hidden';
      e.target.closest('div').classList.remove('_open');
      e.target.closest('.client-pop-up').classList.remove('_open');
      body.classList.remove('lock');
      bodyUnlock();
    });
  }

  function closeOnOverlay() {
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.pop-up-content')) {
        e.target.closest('.client-pop-up').style.overflowY = 'hidden';
        e.target.closest('.client-pop-up').classList.remove('_open');
        e.target
          .closest('.clients-item')
          .querySelector('.pop-up-content')
          .classList.remove('_open');
        body.classList.remove('lock');
        bodyUnlock();
      }
    });
  }

  function bodyLock() {
    const lockPaddingValue = window.innerWidth - body.offsetWidth + 'px';
    body.style.paddingRight = lockPaddingValue;
    for (var i = popUps.length - 1; i >= 0; i--) {
      const el = popUps[i];
      el.style.paddingRight = lockPaddingValue;
    }
  }

  function bodyUnlock() {
    body.style.paddingRight = '0';
    for (var i = popUps.length - 1; i >= 0; i--) {
      const el = popUps[i];
      el.style.paddingRight = 0;
    }
  }

  /* Hamburger */

  let hamburger = document.querySelector('.hamburger'),
    burgerMenu = document.querySelector('.burger-menu'),
    burgerCross = document.querySelector('.burger-cross');
  burgerMenuLinks = document.querySelectorAll('.burger-menu li a');

  hamburger.addEventListener('click', () => {
    burgerMenu.classList.add('_active');
  });
  burgerCross.addEventListener('click', () => {
    burgerMenu.classList.remove('_active');
  });
  burgerMenuLinks.forEach((item) => {
    item.addEventListener('click', () => {
      burgerMenu.classList.remove('_active');
    });
  });

  /* WhatsApp Bubble*/
  let whatsappBubble = document.querySelector('.whatsapp-bubble'),
    whatappPopup = document.querySelector('.whatsapp-pop-up');

  whatsappBubble.addEventListener('click', () => {
    whatsappBubble.classList.toggle('_active');
    whatappPopup.classList.toggle('_active');
  });

  /* Form Become our client*/
  const getQuoteLinks = document.querySelectorAll('.get-quote-link'),
    applyBtn = document.querySelector('.apply_btn'),
    sendInquiry = document.querySelector('.send_inquiry'),
    ourTeamItems = document.querySelectorAll('.our-team-item._hidden');
  let formCross = document.querySelector('.form-content svg'),
    formPopUp = document.querySelector('.form-content'),
    joinBtn = document.querySelector('.clients-join-btn'),
    showAllTeam = document.querySelector('.show-all-team'),
    showMoreLogos = document.querySelector('.show-more-logos'),
    hiddenLogos = document.querySelector('.logos_hidden');

  function closeOverlay() {
    if (formPopUp.classList.contains('_active')) {
      document.addEventListener('click', (e) => {
        if (!formPopUp.contains(e.target)) {
          document.querySelector('.form-pop-up').classList.remove('_active');
          document.querySelector('.form-content').classList.remove('_active');
        }
      });
    }
  }

  getQuoteLinks.forEach((item) => {
    item.addEventListener('click', (e) => {
      e.preventDefault();
      document.querySelector('.form-pop-up').classList.add('_active');
      document.querySelector('.form-content').classList.add('_active');
      e.stopPropagation();
      closeOverlay();
    });
  });

  if (sendInquiry) {
    sendInquiry.addEventListener('click', (e) => {
      document.querySelector('.form-pop-up').classList.add('_active');
      document.querySelector('.form-content').classList.add('_active');
      e.stopPropagation();
      closeOverlay();
    });
  }

  if (applyBtn) {
    applyBtn.addEventListener('click', (e) => {
      document.querySelector('.form-pop-up').classList.add('_active');
      document.querySelector('.form-content').classList.add('_active');
      e.stopPropagation();
      closeOverlay();
    });
  }

  if (joinBtn) {
    joinBtn.addEventListener('click', (e) => {
      document.querySelector('.form-pop-up').classList.add('_active');
      document.querySelector('.form-content').classList.add('_active');
    });
  }

  if (showAllTeam) {
    showAllTeam.addEventListener('click', () => {
      ourTeamItems.forEach((item) => {
        $(item).slideDown();
      });
      showAllTeam.style.display = 'none';
    });
  }

  if (showMoreLogos) {
    showMoreLogos.addEventListener('click', () => {
      $(hiddenLogos).slideDown();
      showMoreLogos.style.display = 'none';
    });
  }

  formCross.addEventListener('click', (e) => {
    e.target.closest('.form-content').classList.remove('_active');
    e.target.closest('.form-pop-up').classList.remove('_active');
  });

  /* Lazy load */
  const images = document.querySelectorAll('.lazy');

  const options = {
    root: null,
    rootMargin: '0px',
    threshold: 0.1,
  };

  function handleImg(myImg, observer) {
    myImg.forEach((myImgSingle) => {
      if (myImgSingle.intersectionRatio > 0) {
        loadImage(myImgSingle.target);
      }
    });
  }

  function loadImage(image) {
    image.src = image.getAttribute('data');
  }

  const observer = new IntersectionObserver(handleImg, options);

  images.forEach((item) => {
    observer.observe(item);
  });

  /* Sending form*/
  const form = document.getElementById('form');
  form.addEventListener('submit', formSend);

  async function formSend(e) {
    e.preventDefault();

    let error = formValidate(form);

    if (error === 0) {
      form.classList.add('_sending');

      if (error === 0) {
        form.reset();
        form.classList.remove('_sending');
        document.querySelector('.form-pop-up').classList.remove('_active');
        document.querySelector('.form-content').classList.remove('_active');
      } else {
        alert('Something went wrong :(');
      }
    } else {
      alert(' Oooops! Something is missing. Make sure all details are there!');
    }
  }

  function formValidate(form) {
    let error = 0;
    let formReq = document.querySelectorAll('._req');

    for (let index = 0; index < formReq.length; index++) {
      const input = formReq[index];
      formRemoveError(input);

      if (input.getAttribute('type') === 'checkbox' && input.checked === false) {
        formAddError(input);
        error++;
      } else {
        if (input.value === '') {
          formAddError(input);
          error++;
        }
      }
    }

    return error;
  }

  function formAddError(input) {
    input.parentElement.classList.add('_error');
    input.parentElement.parentElement.classList.add('_error');
    input.classList.add('_error');
  }
  function formRemoveError(input) {
    input.parentElement.classList.remove('_error');
    input.classList.remove('_error');
  }
});
