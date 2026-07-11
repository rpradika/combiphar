import { Head, useForm, usePage } from "@inertiajs/react"
import { useState, useEffect } from "react"
import SiteLayout from "../Layouts/SiteLayout"
import Modal from "../components/Modal"

export default function Contact({ page, vacancies }) {
  const {
    props: { t, locale, homeUrl, flash },
  } = usePage()
  const en = locale === "en"
  const [tab, setTab] = useState(flash.contact_success ? "kontak" : "karir")
  const [vac, setVac] = useState(null)
  const { data, setData, post, processing, errors, reset } = useForm({
    name: "",
    email: "",
    phone: "",
    subject: "",
    message: "",
  })
  const [vacancyPage, setVacancyPage] = useState(1)
  const scrollToVacancies = () => {
    document.querySelector(".career-vacancies")?.scrollIntoView({
      behavior: "smooth",
      block: "start",
    })
  }

  const VACANCIES_PER_PAGE = 6
  const totalVacancyPages = Math.max(
    1,
    Math.ceil(vacancies.length / VACANCIES_PER_PAGE),
  )
  const pagedVacancies = vacancies.slice(
    (vacancyPage - 1) * VACANCIES_PER_PAGE,
    vacancyPage * VACANCIES_PER_PAGE,
  )

  const [openFaq, setOpenFaq] = useState(2)

  useEffect(() => {
    const els = document.querySelectorAll(".rv")
    els.forEach((el) => el.classList.add("is-in"))
  }, [tab, vacancyPage])

  useEffect(() => {
    if (vacancyPage > totalVacancyPages) {
      setVacancyPage(1)
    }
  }, [vacancyPage, totalVacancyPages])

  const faqs = [
    {
      q: "Lorem ipsum dolor sit amet, consectetur adipiscing elit?",
      a: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sed orci sed dolor sollicitudin eleifend vel vitae odio. Ut at dui eu augue blandit mollis.",
    },
    {
      q: "Lorem ipsum dolor sit amet, consectetur adipiscing elit?",
      a: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
    },
    {
      q: "Lorem ipsum dolor sit amet, consectetur adipiscing elit?",
      a: "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sed orci sed dolor sollicitudin eleifend vel vitae odio.",
    },
    {
      q: "Lorem ipsum dolor sit amet, consectetur adipiscing elit?",
      a: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
    },
  ]

  const submit = (e) => {
    e.preventDefault()
    post(window.location.pathname, {
      preserveScroll: true,
      onSuccess: () => reset(),
    })
  }

  const steps = [
    "Application Review",
    "HR Interview",
    "User Interview",
    "Assessment",
    "Offering",
    "Onboarding",
  ]

  return (
    <>
      <Head title={`${t.nav.contact} — Combiphar`} />

      <section
        className="banner banner--about"
        style={
          page?.bannerImage
            ? {
                backgroundImage: `url('${page.bannerImage}')`,
                backgroundSize: "cover",
                backgroundPosition: "center",
              }
            : {}
        }
      >
        <div className="container">
          <span className="banner__crumb">
            <a href={homeUrl}>Home</a> &rsaquo; {t.nav.contact}
          </span>
          <h1 className="display">{t.nav.contact}</h1>
        </div>
      </section>

      <nav className="subnav" aria-label="Careers and contact">
        <div className="container subnav__inner">
          <button
            type="button"
            className={tab === "karir" ? "on" : ""}
            onClick={() => setTab("karir")}
          >
            {en ? "Careers" : "Karir"}
          </button>
          <button
            type="button"
            className={tab === "kontak" ? "on" : ""}
            onClick={() => setTab("kontak")}
          >
            {en ? "Contact" : "Kontak"}
          </button>
        </div>
      </nav>

      {tab === "karir" && (
        <>
          <section className="section career-vacancies">
            <div className="container">
              <div className="career-vacancies__head rv">
                <div>
                  <h2 className="career-vacancies__title">
                    {en ? "Available Vacancies" : "Lowongan Tersedia"}
                  </h2>
                </div>

                <p className="career-vacancies__intro">
                  {en
                    ? "Find the right position and start your career journey with Combiphar."
                    : "Temukan posisi yang tepat dan mulai karirmu bersama Combiphar."}
                </p>
              </div>

              {vacancies.length === 0 && (
                <p className="lead" style={{ marginTop: 36 }}>
                  {en
                    ? "No open positions right now."
                    : "Belum ada lowongan saat ini."}
                </p>
              )}

              <div className="career-vacancies__grid">
                {pagedVacancies.map((v, i) => (
                  <article className="vac-card rv" key={i}>
                    <h3>{v.title}</h3>

                    {v.location && (
                      <p className="vac-card__meta">
                        <strong>{en ? "Location:" : "Lokasi:"}</strong>{" "}
                        {v.location}
                      </p>
                    )}

                    {v.description && (
                      <p className="vac-card__desc">
                        <strong>
                          {en ? "Responsibilities:" : "Tanggung Jawab:"}
                        </strong>{" "}
                        {v.description}
                      </p>
                    )}

                    <div className="vac-card__actions">
                      <button
                        type="button"
                        className="vac-card__btn"
                        onClick={() => setVac(v)}
                      >
                        {en ? "Detail" : "Detil"}
                      </button>

                      <a
                        className="vac-card__btn"
                        href={v.applyUrl || "#"}
                        target="_blank"
                        rel="noopener noreferrer"
                        onClick={(e) => {
                          if (!v.applyUrl) e.preventDefault()
                        }}
                      >
                        {en ? "Apply" : "Daftar"}
                      </a>
                    </div>
                  </article>
                ))}
              </div>

              {vacancies.length > VACANCIES_PER_PAGE && (
                <div
                  className="career-vacancies__pager rv"
                  aria-label="Vacancy pagination"
                >
                  <span>{en ? "Page:" : "Halaman:"}</span>

                  {Array.from({ length: totalVacancyPages }, (_, i) => {
                    const page = i + 1
                    return (
                      <button
                        key={page}
                        type="button"
                        className={vacancyPage === page ? "is-active" : ""}
                        onClick={() => {
                          setVacancyPage(page)
                          scrollToVacancies();
                        }}
                        aria-current={vacancyPage === page ? "page" : undefined}
                      >
                        {page}
                      </button>
                    )
                  })}
                </div>
              )}
            </div>
          </section>
          <section className="section recruitment-process-section">
            <div className="container">
              <div className="sec-head rv">
                <h2 className="display">Recruitment Process</h2>
              </div>

              <div className="process-cards rv">
                {steps.map((s, i) => (
                  <div className="process-card" key={s}>
                    <div className="process-card__icon">{i + 1}</div>
                    <p>{s}</p>
                  </div>
                ))}
              </div>
            </div>
          </section>
        </>
      )}

      {tab === "kontak" && (
        <>
          <section className="section contact-hero-section">
            <div className="container">
              <div className="contact-intro-grid">
                <div className="contact-copy rv">
                  <span className="eyebrow eyebrow--magenta">
                    {en ? "Contact Us" : "Hubungi Kami"}
                  </span>
                  <h2 className="display">Have a Question?</h2>
                  <p>
                    {en
                      ? "Contact us directly by filling out the form."
                      : "Hubungi kami langsung dengan mengisi formulir."}
                  </p>
                </div>

                <form
                  className="form-wrap rv contact-form-card"
                  onSubmit={submit}
                >
                  {flash.contact_success && (
                    <div className="form-success">
                      {en
                        ? "Thank you! Your message has been sent."
                        : "Terima kasih! Pesan Anda telah terkirim."}
                    </div>
                  )}

                  <div className="form contact-form contact-form--image3">
                    <div className="field">
                      <label htmlFor="nm">
                        {en ? "Your Name" : "Nama Anda"} *
                      </label>
                      <input
                        id="nm"
                        value={data.name}
                        onChange={(e) => setData("name", e.target.value)}
                        placeholder="Type your name"
                        required
                      />
                      {errors.name && <small>{errors.name}</small>}
                    </div>

                    <div className="field">
                      <label htmlFor="em">Email Address *</label>
                      <input
                        id="em"
                        type="email"
                        value={data.email}
                        onChange={(e) => setData("email", e.target.value)}
                        placeholder="Type your email address"
                        required
                      />
                      {errors.email && <small>{errors.email}</small>}
                    </div>

                    <div className="field">
                      <label htmlFor="sb">{en ? "Subject" : "Subjek"} *</label>
                      <input
                        id="sb"
                        value={data.subject}
                        onChange={(e) => setData("subject", e.target.value)}
                        placeholder="Type your subject"
                      />
                    </div>

                    <div className="field">
                      <label htmlFor="ph">
                        {en ? "Phone Number" : "Nomor Telepon"}
                      </label>
                      <input
                        id="ph"
                        value={data.phone}
                        onChange={(e) => setData("phone", e.target.value)}
                        placeholder="Type your phone number"
                      />
                    </div>

                    <div className="field full">
                      <label htmlFor="msg">{en ? "Message" : "Pesan"} *</label>
                      <textarea
                        id="msg"
                        value={data.message}
                        onChange={(e) => setData("message", e.target.value)}
                        placeholder="Enter your message here ..."
                        required
                      />
                      {errors.message && <small>{errors.message}</small>}
                    </div>

                    <div className="full contact-form__meta">
                      <label className="contact-consent">
                        <input type="checkbox" required />
                        <span>
                          {en
                            ? "I’ve read and agree with "
                            : "Saya telah membaca dan menyetujui "}
                          <a href="/terms-of-use">Terms of Services</a> and{" "}
                          <a href="/privacy-notice">Privacy Policy</a>
                        </span>
                      </label>
                    </div>

                    <div className="full contact-form__actions">
                      <button
                        className="btn btn--outline contact-submit"
                        type="submit"
                        disabled={processing}
                      >
                        {en ? "Send Message" : "Kirim Pesan"}
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </section>

          <section className="section recruitment-process-section">
            <div className="container">
              <div className="sec-head rv">
                <h2 className="display">Recruitment Process</h2>
              </div>

              <div className="process-cards rv">
                {steps.map((s, i) => (
                  <div className="process-card" key={s}>
                    <div className="process-card__icon">{i + 1}</div>
                    <p>{s}</p>
                  </div>
                ))}
              </div>
            </div>
          </section>

          <section className="section faq-section">
            <div className="container faq-wrap">
              <div className="sec-head rv">
                <h2 className="display">Frequently Asked Question (FAQ)</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
              </div>

              <div className="faq-list rv">
                {faqs.map((item, i) => {
                  const isOpen = openFaq === i
                  return (
                    <div
                      key={i}
                      className={`faq-item ${isOpen ? "is-open" : ""}`}
                    >
                      <button
                        type="button"
                        className="faq-q"
                        onClick={() => setOpenFaq(isOpen ? null : i)}
                        aria-expanded={isOpen}
                      >
                        <span>{item.q}</span>
                        <span className="faq-icon">{isOpen ? "−" : "+"}</span>
                      </button>
                      {isOpen && (
                        <div className="faq-a">
                          <p>{item.a}</p>
                        </div>
                      )}
                    </div>
                  )
                })}
              </div>
            </div>
          </section>
        </>
      )}

      <section className="warning">
        <div className="container warning__inner">
          <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
          >
            <path d="M12 9v4m0 4h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" />
          </svg>
          <div>
            <h3>
              {en
                ? "Official Combiphar recruitment information is only available here. Don't fall for scams impersonating us."
                : "Informasi rekrutmen resmi Combiphar hanya tersedia di sini. Jangan tertipu penipuan yang mengatasnamakan kami."}
            </h3>
            {/* <p>
              {en
                ? "Combiphar never charges any fees during recruitment. All official recruitment is conducted only through Combiphar official channels."
                : "Combiphar tidak pernah memungut biaya apa pun selama proses rekrutmen. Seluruh proses rekrutmen resmi hanya dilakukan melalui kanal resmi Combiphar."}
            </p> */}
          </div>
        </div>
      </section>

      <Modal open={!!vac} onClose={() => setVac(null)} closeLabel={t.close}>
        {vac && (
          <div className="vac-modal">
            <h3>{vac.title}</h3>
            <p
              style={{
                fontWeight: 600,
                color: "var(--magenta)",
                marginBottom: 14,
              }}
            >
              {[vac.location, vac.department].filter(Boolean).join("  ·  ")}
            </p>
            <div
              style={{
                whiteSpace: "pre-line",
                color: "var(--text-muted)",
                lineHeight: 1.7,
              }}
            >
              {vac.description}
            </div>
          </div>
        )}
      </Modal>
    </>
  )
}

Contact.layout = (page) => <SiteLayout>{page}</SiteLayout>
