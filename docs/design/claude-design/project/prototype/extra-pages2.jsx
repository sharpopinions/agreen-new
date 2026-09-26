// ── EXTRA PAGES 2 ────────────────────────────────────────────────
// Compare, Vacancies, Login, Registration, Thank pages, Privacy/Legal

// ── COMPARE ──────────────────────────────────────────────────────
function ComparePage({ setPage, addToCart }) {
  const { T } = useContext(ThemeCtx);
  const [items, setItems] = useState(PRODUCTS.slice(0, 3));

  const specs = [
    ['Серія',          ['Image Design',   'Performance',    'Standard']],
    ['Матеріал',       ['Метал/пластик',  'Пластик',        'Метал']],
    ['Колір',          ['Сталевий',       'Білий/бірюза',   'Чорний']],
    ['Розмір В×Ш×Г',  ['373×345×204',    '310×285×180',    '290×260×160']],
    ['Система',        ['H1',             'H2',             'H1']],
    ['Живлення',       ['4×AA',           'від мережі',     '4×AA']],
    ['Гарантія',       ['2 роки',         '1 рік',          '2 роки']],
    ['Рейтинг',        ['4.2/5',          '4.8/5',          '4.1/5']],
  ];

  if (items.length === 0) return (
    <div style={{ background: T.bg, minHeight: '55vh', display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', gap: 16 }}>
      <div style={{ width: 64, height: 64, background: T.muted, borderRadius: T.rLg, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke={T.text3} strokeWidth="1.5">
          <path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/>
        </svg>
      </div>
      <div style={{ fontSize: 18, fontWeight: 600, color: T.text }}>Порівняння товарів</div>
      <div style={{ fontSize: 14, color: T.text2 }}>Додайте товари для порівняння</div>
      <Btn onClick={() => setPage('catalog')}>До каталогу</Btn>
    </div>
  );

  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Порівняння товарів']} setPage={setPage}/>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'baseline', marginBottom: 24 }}>
        <h1 style={{ fontSize: 24, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', fontFamily: 'Geist, Inter, sans-serif' }}>Порівняння товарів</h1>
        <div style={{ display: 'flex', gap: 12, alignItems: 'center' }}>
          <span style={{ fontSize: 13, color: T.text2 }}>Показувати:</span>
          <select style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rSm, padding: '6px 10px', color: T.text2, fontSize: 13, outline: 'none', fontFamily: 'Geist, Inter, sans-serif' }}>
            <option>Всі характеристики</option>
            <option>Тільки відмінності</option>
          </select>
          <select style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rSm, padding: '6px 10px', color: T.text2, fontSize: 13, outline: 'none', fontFamily: 'Geist, Inter, sans-serif' }}>
            <option>Диспенсери</option>
            <option>Клеї-герметики</option>
          </select>
        </div>
      </div>

      {/* Products header row */}
      <div style={{ display: 'grid', gridTemplateColumns: `200px repeat(${items.length}, 1fr)`, gap: 1, marginBottom: 2 }}>
        <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: `${T.rLg} 0 0 0`, padding: 16 }}/>
        {items.map((p, i) => (
          <div key={p.id} style={{ background: T.surface, border: `1px solid ${T.border}`,
            borderLeft: 'none', borderRadius: i === items.length-1 ? `0 ${T.rLg} 0 0` : 0,
            padding: 16, position: 'relative' }}>
            <button onClick={() => setItems(prev => prev.filter(x => x.id !== p.id))}
              style={{ position: 'absolute', top: 8, right: 8, background: T.muted, border: `1px solid ${T.border}`, borderRadius: T.rSm, width: 24, height: 24, display: 'flex', alignItems: 'center', justifyContent: 'center', cursor: 'pointer' }}>
              <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke={T.text3} strokeWidth="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <Img h={120} label="фото"/>
            <div style={{ fontSize: 11, color: T.text3, marginTop: 8, marginBottom: 4 }}>Арт: {p.sku}</div>
            <div style={{ fontSize: 13, fontWeight: 500, color: T.text, lineHeight: 1.4, marginBottom: 8 }}>{p.name}</div>
            <Stars rating={p.rating}/>
            <div style={{ fontSize: 18, fontWeight: 700, color: T.text, marginTop: 8, marginBottom: 10, letterSpacing: '-0.025em' }}>{fmt(p.price)}</div>
            <Btn full size="sm" onClick={() => { addToCart(p); setPage('cart'); }}>До кошика</Btn>
          </div>
        ))}
        {/* Add column */}
        {items.length < 4 && (
          <div style={{ background: T.bgAlt, border: `1px solid ${T.border}`, borderLeft: 'none',
            borderRadius: `0 ${T.rLg} 0 0`, padding: 16, display: 'flex', flexDirection: 'column',
            alignItems: 'center', justifyContent: 'center', gap: 8, cursor: 'pointer', minHeight: 240 }}
            onClick={() => setPage('catalog')}>
            <div style={{ width: 40, height: 40, border: `1.5px dashed ${T.border2}`, borderRadius: T.rSm, display: 'flex', alignItems: 'center', justifyContent: 'center', color: T.text3, fontSize: 24 }}>+</div>
            <div style={{ fontSize: 12, color: T.text3, textAlign: 'center' }}>Додати товар</div>
          </div>
        )}
      </div>

      {/* Specs rows */}
      <div style={{ border: `1px solid ${T.border}`, borderRadius: `0 0 ${T.rLg} ${T.rLg}`, overflow: 'hidden' }}>
        <div style={{ padding: '10px 16px', background: T.muted, fontSize: 12, fontWeight: 600, color: T.text2, textTransform: 'uppercase', letterSpacing: '0.08em' }}>Характеристики</div>
        {specs.map(([label, vals], idx) => (
          <div key={label} style={{ display: 'grid', gridTemplateColumns: `200px repeat(${items.length}, 1fr)${items.length < 4 ? ' 1fr' : ''}`,
            borderTop: `1px solid ${T.border}`,
            background: idx % 2 === 0 ? T.surface : T.bgAlt }}>
            <div style={{ padding: '12px 16px', fontSize: 13, fontWeight: 500, color: T.text2 }}>{label}</div>
            {vals.slice(0, items.length).map((v, i) => (
              <div key={i} style={{ padding: '12px 16px', fontSize: 13, color: T.text, borderLeft: `1px solid ${T.border}` }}>{v}</div>
            ))}
            {items.length < 4 && <div style={{ borderLeft: `1px solid ${T.border}` }}/>}
          </div>
        ))}
      </div>
    </div>
  );
}

// ── VACANCIES ─────────────────────────────────────────────────────
function VacanciesPage({ setPage }) {
  const { T } = useContext(ThemeCtx);
  const [open, setOpen] = useState(null);
  const [form, setForm] = useState({ name:'', phone:'', email:'', position:'', msg:'' });
  const [sent, setSent] = useState(false);
  const upd = (k,v) => setForm(f => ({ ...f, [k]: v }));

  const vacancies = [
    { id:1, title:'Менеджер з продажу B2B', dept:'Відділ продажів', type:'Повна зайнятість', city:'Київ',
      desc:'Шукаємо досвідченого менеджера для роботи з корпоративними клієнтами. Досвід продажів B2B від 2 років.',
      req:['Досвід активних продажів B2B від 2 років','Знання ринку промислових товарів буде перевагою','Комунікабельність, цілеспрямованість','Водійські права кат. B'] },
    { id:2, title:'Технічний спеціаліст',   dept:'Технічний відділ', type:'Повна зайнятість', city:'Київ',
      desc:'Технічна підтримка клієнтів, монтаж та обслуговування обладнання. Відрядження по Україні.',
      req:['Технічна освіта (механіка, електроніка)','Досвід роботи з промисловим обладнанням','Наявність водійських прав','Готовність до відряджень'] },
    { id:3, title:'Логіст / комірник',       dept:'Складська служба', type:'Повна зайнятість', city:'Київ',
      desc:'Організація складської логістики, прийом та відпуск товару, робота з WMS-системою.',
      req:['Досвід роботи на складі від 1 року','Знання 1С або аналогів','Відповідальність, уважність до деталей','Фізична витривалість'] },
    { id:4, title:'Маркетолог / SMM',        dept:'Маркетинг', type:'Повна зайнятість / Remote', city:'Київ / Remote',
      desc:'Розробка та реалізація маркетингової стратегії, ведення соціальних мереж, email-маркетинг.',
      req:['Досвід у digital-маркетингу від 2 років','Знання Google Ads, Meta Ads','Навички написання текстів','Знання англійської мови'] },
  ];

  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Про компанію','Вакансії']} setPage={setPage}/>

      {/* Banner */}
      <div style={{ borderRadius: T.rLg, overflow: 'hidden', marginBottom: 40 }}>
        <Img h={260} label="банер вакансій — команда компанії"/>
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 48, marginBottom: 48 }}>
        <div>
          <h1 style={{ fontSize: 28, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', marginBottom: 16, fontFamily: 'Geist, Inter, sans-serif' }}>Приєднуйтесь до команди A-green</h1>
          <p style={{ fontSize: 14, color: T.text2, lineHeight: 1.8, marginBottom: 20 }}>
            Ми шукаємо талановитих та мотивованих людей, які хочуть розвиватись у сфері промислових товарів. Пропонуємо конкурентну заробітну плату, соціальний пакет та можливості для кар'єрного зростання.
          </p>
          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
            {[['Конкурентна ЗП','і квартальні бонуси'],['Медичне страхування','для всіх співробітників'],['Навчання','за рахунок компанії'],['Дружня команда','і корпоративна культура']].map(([t,s]) => (
              <div key={t} style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: '14px 16px' }}>
                <div style={{ fontSize: 13, fontWeight: 600, color: T.text, marginBottom: 2 }}>{t}</div>
                <div style={{ fontSize: 12, color: T.text2 }}>{s}</div>
              </div>
            ))}
          </div>
        </div>
        {/* Quick apply */}
        {!sent ? (
          <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 28 }}>
            <h3 style={{ fontSize: 16, fontWeight: 600, color: T.text, marginBottom: 16 }}>Не знайшли підходящу вакансію?</h3>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
              <Field label="Ім'я *" placeholder="Іван Іваненко" value={form.name} onChange={e => upd('name',e.target.value)}/>
              <Field label="Телефон *" placeholder="+380 XX XXX XX XX" value={form.phone} onChange={e => upd('phone',e.target.value)}/>
              <Field label="Email *" placeholder="email@gmail.com" type="email" value={form.email} onChange={e => upd('email',e.target.value)}/>
              <Field label="Бажана посада" placeholder="Менеджер, спеціаліст..." value={form.position} onChange={e => upd('position',e.target.value)}/>
              <Btn onClick={() => setSent(true)}>Надіслати резюме</Btn>
            </div>
          </div>
        ) : (
          <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 28, display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', textAlign: 'center', gap: 12 }}>
            <div style={{ fontSize: 40 }}>👏</div>
            <h3 style={{ fontSize: 16, fontWeight: 600, color: T.text }}>Дякуємо за відгук!</h3>
            <p style={{ fontSize: 14, color: T.text2 }}>Ми розглянемо вашу кандидатуру та зв'яжемось з вами.</p>
          </div>
        )}
      </div>

      {/* Vacancies list */}
      <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text, letterSpacing: '-0.02em', marginBottom: 20, fontFamily: 'Geist, Inter, sans-serif' }}>Відкриті вакансії</h2>
      <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
        {vacancies.map(v => (
          <div key={v.id} style={{ background: T.surface, border: `1px solid ${open===v.id ? T.border2 : T.border}`, borderRadius: T.rLg, overflow: 'hidden', transition: 'all 0.2s', boxShadow: T.shadow }}>
            <div onClick={() => setOpen(open===v.id ? null : v.id)}
              style={{ padding: '18px 24px', cursor: 'pointer', display: 'flex', alignItems: 'center', gap: 16 }}>
              <div style={{ flex: 1 }}>
                <div style={{ fontSize: 16, fontWeight: 600, color: T.text, marginBottom: 4 }}>{v.title}</div>
                <div style={{ display: 'flex', gap: 12, fontSize: 12, color: T.text2 }}>
                  <span>{v.dept}</span>
                  <span style={{ color: T.border2 }}>·</span>
                  <span>{v.city}</span>
                  <span style={{ color: T.border2 }}>·</span>
                  <span>{v.type}</span>
                </div>
              </div>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke={T.text3} strokeWidth="2"
                style={{ transform: open===v.id ? 'rotate(180deg)' : 'none', transition: 'transform 0.2s', flexShrink: 0 }}>
                <path d="m6 9 6 6 6-6"/>
              </svg>
            </div>
            {open===v.id && (
              <div style={{ borderTop: `1px solid ${T.border}`, padding: '20px 24px', display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 32 }}>
                <div>
                  <div style={{ fontSize: 14, fontWeight: 600, color: T.text, marginBottom: 10 }}>Про вакансію</div>
                  <p style={{ fontSize: 13, color: T.text2, lineHeight: 1.7, marginBottom: 16 }}>{v.desc}</p>
                  <div style={{ fontSize: 14, fontWeight: 600, color: T.text, marginBottom: 10 }}>Вимоги</div>
                  {v.req.map(r => (
                    <div key={r} style={{ display: 'flex', gap: 8, marginBottom: 6, fontSize: 13, color: T.text2 }}>
                      <span style={{ color: T.text3 }}>—</span>{r}
                    </div>
                  ))}
                </div>
                <div>
                  <div style={{ fontSize: 14, fontWeight: 600, color: T.text, marginBottom: 14 }}>Відгукнутись на вакансію</div>
                  <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                    <Field label="Ім'я *" placeholder="Іван Іваненко"/>
                    <Field label="Телефон *" placeholder="+380 XX XXX XX XX"/>
                    <Field label="Email *" placeholder="email@gmail.com" type="email"/>
                    <Btn onClick={() => { setOpen(null); setSent(true); }}>Відгукнутись</Btn>
                  </div>
                </div>
              </div>
            )}
          </div>
        ))}
      </div>
    </div>
  );
}

// ── LOGIN ─────────────────────────────────────────────────────────
function LoginPage({ setPage }) {
  const { T } = useContext(ThemeCtx);
  const [form, setForm] = useState({ login:'', pass:'' });
  const [tab, setTab] = useState('login');
  const upd = (k,v) => setForm(f => ({ ...f, [k]: v }));

  return (
    <div style={{ background: T.bg, minHeight: '80vh', display: 'flex', alignItems: 'center', justifyContent: 'center', padding: '40px 60px' }}>
      <div style={{ width: 760, display: 'grid', gridTemplateColumns: '1fr 1fr', background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, overflow: 'hidden', boxShadow: T.shadowMd }}>
        {/* Left: form */}
        <div style={{ padding: 40 }}>
          {/* Tabs */}
          <div style={{ display: 'flex', marginBottom: 28, gap: 0, borderBottom: `1px solid ${T.border}` }}>
            {[['login','Вхід'],['register','Реєстрація']].map(([k,l]) => (
              <button key={k} onClick={() => setTab(k)}
                style={{ padding: '10px 20px', background: 'none', border: 'none', cursor: 'pointer',
                  fontSize: 14, fontWeight: tab===k ? 600 : 400,
                  color: tab===k ? T.text : T.text2, fontFamily: 'Geist, Inter, sans-serif',
                  borderBottom: `2px solid ${tab===k ? T.text : 'transparent'}`, marginBottom: -1 }}>
                {l}
              </button>
            ))}
          </div>

          {tab === 'login' ? (
            <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
              <Field label="Email або телефон *" placeholder="email@company.ua або +380..." value={form.login} onChange={e => upd('login',e.target.value)}/>
              <Field label="Пароль *" placeholder="••••••••" type="password" value={form.pass} onChange={e => upd('pass',e.target.value)}/>
              <div style={{ display: 'flex', justifyContent: 'flex-end' }}>
                <span style={{ fontSize: 12, color: T.text2, cursor: 'pointer', textDecoration: 'underline', textUnderlineOffset: 2 }}>Забули пароль?</span>
              </div>
              <Btn full onClick={() => setPage('dashboard')}>Увійти</Btn>
              <div style={{ textAlign: 'center', fontSize: 13, color: T.text2 }}>
                Немає акаунту?{' '}
                <span onClick={() => setTab('register')} style={{ color: T.text, fontWeight: 600, cursor: 'pointer', textDecoration: 'underline', textUnderlineOffset: 2 }}>Зареєструватись</span>
              </div>
            </div>
          ) : (
            <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
              <Field label="Назва компанії *" placeholder="ТОВ 'Назва'"/>
              <Field label="Контактна особа *" placeholder="Іван Іваненко"/>
              <Field label="Телефон *" placeholder="+380 XX XXX XX XX"/>
              <Field label="Email *" placeholder="email@company.ua" type="email"/>
              <Field label="Пароль *" placeholder="Мінімум 8 символів" type="password"/>
              <div style={{ fontSize: 12, color: T.text3, lineHeight: 1.6 }}>
                Реєструючись, ви погоджуєтесь з{' '}
                <span style={{ color: T.text2, textDecoration: 'underline', cursor: 'pointer' }}>умовами використання</span>
              </div>
              <Btn full onClick={() => setPage('dashboard')}>Зареєструватись</Btn>
            </div>
          )}
        </div>
        {/* Right: image + info */}
        <div style={{ background: T.bgAlt, borderLeft: `1px solid ${T.border}`, padding: 40, display: 'flex', flexDirection: 'column', justifyContent: 'center', gap: 20 }}>
          <Img h={180} label="партнер A-green"/>
          <div style={{ fontSize: 16, fontWeight: 700, color: T.text, letterSpacing: '-0.01em' }}>Особистий кабінет партнера</div>
          <div style={{ fontSize: 13, color: T.text2, lineHeight: 1.7 }}>
            Отримайте доступ до партнерських цін, управління замовленнями та документами.
          </div>
          {[['Знижки до 25%','на весь асортимент'],['Персональний менеджер','завжди на зв\'язку'],['Документообіг','онлайн в кабінеті']].map(([t,s]) => (
            <div key={t} style={{ display: 'flex', gap: 10, alignItems: 'flex-start' }}>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke={T.text} strokeWidth="2.5" style={{ marginTop: 2, flexShrink: 0 }}><polyline points="20 6 9 17 4 12"/></svg>
              <div><span style={{ fontSize: 13, fontWeight: 600, color: T.text }}>{t}</span><span style={{ fontSize: 13, color: T.text2 }}> — {s}</span></div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

// ── THANK ORDER ───────────────────────────────────────────────────
function ThankOrderPage({ setPage }) {
  const { T } = useContext(ThemeCtx);
  return (
    <div style={{ background: T.bg, minHeight: '60vh', display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', padding: '60px 60px', textAlign: 'center' }}>
      <div style={{ width: 72, height: 72, background: T.bg==='#09090b'?'#052e16':'#f0fdf4', border: `1px solid ${T.bg==='#09090b'?'#14532d':'#bbf7d0'}`, borderRadius: T.rLg, display: 'flex', alignItems: 'center', justifyContent: 'center', marginBottom: 24 }}>
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16a34a" strokeWidth="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
      <h1 style={{ fontSize: 28, fontWeight: 700, color: T.text, marginBottom: 10, letterSpacing: '-0.025em', fontFamily: 'Geist, Inter, sans-serif' }}>Дякуємо за замовлення!</h1>
      <p style={{ fontSize: 15, color: T.text2, marginBottom: 6 }}>Ваше замовлення №12345 успішно оформлене.</p>
      <p style={{ fontSize: 14, color: T.text3, marginBottom: 32 }}>Статус: В обробці · Орієнтовна доставка: 3–5 робочих днів</p>

      {/* Order summary */}
      <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 28, width: '100%', maxWidth: 640, textAlign: 'left', marginBottom: 28 }}>
        <div style={{ fontSize: 14, fontWeight: 600, color: T.text, marginBottom: 16 }}>Деталі замовлення</div>
        <div style={{ display: 'grid', gridTemplateColumns: '1fr auto auto', gap: '8px 16px', fontSize: 13, borderBottom: `1px solid ${T.border}`, paddingBottom: 14, marginBottom: 14 }}>
          <span style={{ color: T.text2, fontWeight: 600 }}>Товар</span>
          <span style={{ color: T.text2, fontWeight: 600 }}>Кількість</span>
          <span style={{ color: T.text2, fontWeight: 600 }}>Сума</span>
          {PRODUCTS.slice(0,2).map(p => (
            <React.Fragment key={p.id}>
              <span style={{ color: T.text }}>{p.name}</span>
              <span style={{ color: T.text2, textAlign: 'center' }}>1</span>
              <span style={{ color: T.text, fontWeight: 500 }}>{fmt(p.price)}</span>
            </React.Fragment>
          ))}
        </div>
        <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: 16, fontWeight: 700, color: T.text, letterSpacing: '-0.02em' }}>
          <span>Разом:</span><span>{fmt(PRODUCTS[0].price + PRODUCTS[1].price)}</span>
        </div>
      </div>

      <div style={{ display: 'flex', gap: 12 }}>
        <Btn onClick={() => setPage('main')}>На головну</Btn>
        <Btn variant="outline" onClick={() => setPage('catalog')}>Продовжити покупки</Btn>
      </div>
    </div>
  );
}

// ── THANK FORM SUBMIT ─────────────────────────────────────────────
function ThankFormPage({ setPage }) {
  const { T } = useContext(ThemeCtx);
  return (
    <div style={{ background: T.bg, minHeight: '60vh', display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', padding: '60px 60px', textAlign: 'center' }}>
      <div style={{ fontSize: 60, marginBottom: 20 }}>📬</div>
      <h1 style={{ fontSize: 28, fontWeight: 700, color: T.text, marginBottom: 10, letterSpacing: '-0.025em', fontFamily: 'Geist, Inter, sans-serif' }}>Повідомлення надіслано!</h1>
      <p style={{ fontSize: 15, color: T.text2, marginBottom: 6 }}>Дякуємо за звернення. Ми отримали вашу заявку.</p>
      <p style={{ fontSize: 14, color: T.text3, marginBottom: 32 }}>Наш менеджер зв'яжеться з вами протягом одного робочого дня.</p>
      <div style={{ display: 'flex', gap: 12 }}>
        <Btn onClick={() => setPage('main')}>На головну</Btn>
        <Btn variant="outline" onClick={() => setPage('contacts')}>Повернутись до контактів</Btn>
      </div>
    </div>
  );
}

// ── PRIVACY POLICY (static) ───────────────────────────────────────
function StaticPage({ setPage, title, crumb, sections }) {
  const { T } = useContext(ThemeCtx);
  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={[crumb]} setPage={setPage}/>
      <div style={{ display: 'grid', gridTemplateColumns: '220px 1fr', gap: 32 }}>
        {/* Sidebar TOC */}
        <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 20, height: 'fit-content', position: 'sticky', top: 80 }}>
          <div style={{ fontSize: 12, fontWeight: 600, color: T.text3, textTransform: 'uppercase', letterSpacing: '0.08em', marginBottom: 14 }}>Зміст</div>
          {CATEGORIES.slice(0,7).map((cat,i) => (
            <div key={cat.id} style={{ padding: '6px 0', fontSize: 13, color: T.text2, cursor: 'pointer', borderBottom: i<6?`1px solid ${T.border}`:'none', transition: 'color 0.12s' }}
              onMouseEnter={e=>e.target.style.color=T.text} onMouseLeave={e=>e.target.style.color=T.text2}>
              {i+1}. {cat.name.substring(0,22)}
            </div>
          ))}
        </div>
        {/* Content */}
        <div>
          <h1 style={{ fontSize: 28, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', marginBottom: 32, fontFamily: 'Geist, Inter, sans-serif' }}>{title}</h1>
          {sections.map((s, i) => (
            <div key={i} style={{ marginBottom: 32 }}>
              <h2 style={{ fontSize: 18, fontWeight: 600, color: T.text, marginBottom: 12, letterSpacing: '-0.01em' }}>{i+1}. {s.title}</h2>
              <p style={{ fontSize: 14, color: T.text2, lineHeight: 1.85 }}>{s.text}</p>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

const PRIVACY_SECTIONS = [
  { title:'Загальні положення', text:'Ця Політика конфіденційності регулює порядок збору, використання та захисту персональних даних користувачів сайту A-green. Використовуючи наш сайт, ви погоджуєтесь з умовами цієї Політики.' },
  { title:'Збір персональних даних', text:'Ми збираємо персональні дані, які ви надаєте нам добровільно при реєстрації, оформленні замовлення або зверненні до служби підтримки: ім\'я, email, телефон, адреса доставки, реквізити компанії.' },
  { title:'Використання даних', text:'Персональні дані використовуються для обробки замовлень, зв\'язку з вами, надсилання повідомлень про статус замовлення, та з вашої згоди — для маркетингових розсилок.' },
  { title:'Захист даних', text:'Ми вживаємо технічних та організаційних заходів для захисту персональних даних від несанкціонованого доступу, зміни, розкриття або знищення. Доступ до даних мають лише уповноважені співробітники.' },
  { title:'Права суб\'єктів даних', text:'Ви маєте право на доступ до своїх персональних даних, їх виправлення, видалення, обмеження обробки та заперечення проти обробки. Для реалізації цих прав зверніться до нас за контактами на сайті.' },
];

const OFFER_SECTIONS = [
  { title:'Предмет договору', text:'Цей Договір публічної оферти є офіційною пропозицією A-green укласти договір купівлі-продажу товарів, представлених на сайті. Акцептом цього договору є оформлення замовлення.' },
  { title:'Ціна та оплата', text:'Ціна товарів вказана на сайті та може бути змінена без попереднього повідомлення. Оплата здійснюється у гривнях за вибраним методом оплати під час оформлення замовлення.' },
  { title:'Доставка', text:'Доставка здійснюється службами доставки, вказаними на сайті. Терміни та вартість доставки залежать від місця призначення та обраного методу доставки. Ризик випадкової загибелі переходить до покупця з моменту передачі товару.' },
  { title:'Повернення та обмін', text:'Повернення товару здійснюється відповідно до Закону України "Про захист прав споживачів". Товар належної якості може бути повернений протягом 14 днів з дати отримання за умови збереження товарного вигляду.' },
  { title:'Відповідальність сторін', text:'Продавець відповідає за якість товарів відповідно до наданих гарантій. У разі виявлення недоліків протягом гарантійного строку, продавець зобов\'язується виправити їх або замінити товар.' },
];

const RETURN_SECTIONS = [
  { title:'Умови повернення', text:'Повернення товару можливе протягом 14 днів з дати отримання. Товар повинен бути у початковому стані, зі збереженням упаковки, ярликів та документів. Повернення ініціює покупець, зв\'язавшись з нашою службою підтримки.' },
  { title:'Процедура повернення', text:'Для повернення товару зверніться до служби підтримки та отримайте номер повернення. Відправте товар на нашу адресу з зазначенням номера повернення. Ми перевіримо товар та повернемо кошти протягом 5–7 робочих днів.' },
  { title:'Повернення коштів', text:'Кошти повертаються тим самим способом, яким була здійснена оплата, протягом 5–7 робочих днів після отримання товару та підтвердження його стану.' },
];

function PrivacyPolicyPage({ setPage }) {
  return <StaticPage setPage={setPage} title="Політика конфіденційності" crumb="Політика конфіденційності" sections={PRIVACY_SECTIONS}/>;
}
function PublicOfferPage({ setPage }) {
  return <StaticPage setPage={setPage} title="Договір публічної оферти" crumb="Договір публічної оферти" sections={OFFER_SECTIONS}/>;
}
function ReturnPage({ setPage }) {
  return <StaticPage setPage={setPage} title="Повернення та обмін" crumb="Повернення та обмін" sections={RETURN_SECTIONS}/>;
}

Object.assign(window, {
  ComparePage, VacanciesPage, LoginPage,
  ThankOrderPage, ThankFormPage,
  PrivacyPolicyPage, PublicOfferPage, ReturnPage,
});
