const revealItems = document.querySelectorAll('.section, .hero-card, .project-card, .about-card, .contact-card');

const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
      }
    });
  },
  { threshold: 0.2 }
);

revealItems.forEach((item) => {
  item.classList.add('reveal');
  observer.observe(item);
});
