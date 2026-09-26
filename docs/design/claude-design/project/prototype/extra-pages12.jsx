// ── EXTRA PAGES 12 ─────────────────────────────────────────────────
// Dashboard variations: rules-2/3, support2-6, role-selection-2/3

// ── DASHBOARD RULES — different state (програма лояльності) ─────
function DashboardRulesProgram({ setPage }) {
  const { T } = useContext(ThemeCtx);
  return (
    <div style={{ background: T.bg, minHeight: 'calc(100vh - 52px)',
      display: 'grid', gridTemplateColumns: '220px 1fr' }}>
      <DashboardSidebar setPage={setPage} active="rules-program"/>
      <div style={{ padding: 32 }}>
        <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text,
          letterSpacing: '-0.025em', marginBottom: 8,
          fontFamily: 'Geist, Inter, sans-serif' }}>Програма лояльності</h2>
        <p style={{ fontSize: 14, color: T.text2, marginBottom: 24 }}>
          Ваш статус та бонусні бали
        </p>

        {/* Status card */}
        <div style={{ background: 'linear-gradient(135deg, #1e293b, #0f172a)',
          border: `1px solid ${T.border2}`, borderRadius: T.rLg, padding: 32,
          marginBottom: 24, color: '#fff', position: 'relative', overflow: 'hidden' }}>
          <div style={{ position: 'absolute', top:-40, right:-40, width: 200, height: 200,
            borderRadius: '50%', background: 'radial-gradient(circle, rgba(255,215,0,0.15), transparent 70%)' }}/>
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', position: 'relative' }}>
            <div>
              <div style={{ fontSize: 11, fontWeight: 600, letterSpacing: '0.1em',
                color: 'rgba(255,255,255,0.6)', textTransform: 'uppercase', marginBottom: 8 }}>
                Поточний рівень
              </div>
              <div style={{ fontSize: 32, fontWeight: 800, letterSpacing: '-0.03em',
                fontFamily: 'Geist, Inter, sans-serif' }}>Gold</div>
              <div style={{ fontSize: 13, color: 'rgba(255,255,255,0.7)', marginTop: 6 }}>
                Знижка 15% на весь асортимент
              </div>
            </div>
            <div style={{ textAlign: 'right' }}>
              <div style={{ fontSize: 11, fontWeight: 600, letterSpacing: '0.1em',
                color: 'rgba(255,255,255,0.6)', textTransform: 'uppercase', marginBottom: 8 }}>
                Бонусних балів
              </div>
              <div style={{ fontSize: 32, fontWeight: 800, letterSpacing: '-0.03em',
                fontFamily: 'Geist, Inter, sans-serif', color: '#fbbf24' }}>2 480</div>
              <div style={{ fontSize: 13, color: 'rgba(255,255,255,0.7)', marginTop: 6 }}>
                ≈ 2 480 ₴
              </div>
            </div>
          </div>

          {/* Progress to Platinum */}
          <div style={{ marginTop: 24, position: 'relative' }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: 12,
              marginBottom: 6, color: 'rgba(255,255,255,0.85)' }}>
              <span>До рівня Platinum</span>
              <span>156 000 / 200 000 ₴</span>
            </div>
            <div style={{ height: 6, background: 'rgba(255,255,255,0.15)', borderRadius: T.rPill }}>
              <div style={{ width: '78%', height: '100%',
                background: 'linear-gradient(90deg, #fbbf24, #f59e0b)',
                borderRadius: T.rPill }}/>
            </div>
          </div>
        </div>

        {/* Levels */}
        <h3 style={{ fontSize: 16, fontWeight: 600, color: T.text,
          letterSpacing: '-0.01em', marginBottom: 14 }}>Рівні програми</h3>
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 12, marginBottom: 32 }}>
          {[
            { name:'Bronze',   from:'0',       discount:'5%',  color:'#cd7f32' },
            { name:'Silver',   from:'50 000',  discount:'10%', color:'#94a3b8' },
            { name:'Gold',     from:'150 000', discount:'15%', color:'#fbbf24', active:true },
            { name:'Platinum', from:'200 000', discount:'25%', color:'#a78bfa' },
          ].map(l => (
            <div key={l.name} style={{ background: T.surface, border: `1px solid ${l.active ? l.color : T.border}`,
              borderRadius: T.rLg, padding: 20,
              boxShadow: l.active ? `0 0 0 1px ${l.color}` : T.shadow, position:'relative' }}>
              {l.active && (
                <div style={{ position: 'absolute', top: 12, right: 12, padding:'2px 8px',
                  background: l.color, color: '#fff', fontSize: 10, fontWeight: 600,
                  letterSpacing: '0.06em', textTransform: 'uppercase',
                  borderRadius: T.rPill }}>Активний</div>
              )}
              <div style={{ width: 32, height: 32, borderRadius: '50%',
                background: l.color, marginBottom: 12, opacity: l.active ? 1 : 0.6 }}/>
              <div style={{ fontSize: 15, fontWeight: 700, color: T.text,
                fontFamily: 'Geist, Inter, sans-serif' }}>{l.name}</div>
              <div style={{ fontSize: 11, color: T.text3, marginTop: 2 }}>від {l.from} ₴</div>
              <div style={{ marginTop: 12, paddingTop: 12, borderTop: `1px solid ${T.border}`,
                display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <span style={{ fontSize: 11, color: T.text3 }}>Знижка</span>
                <span style={{ fontSize: 18, fontWeight: 700, color: T.text,
                  fontFamily: 'Geist, Inter, sans-serif' }}>{l.discount}</span>
              </div>
            </div>
          ))}
        </div>

        {/* Bonus history */}
        <h3 style={{ fontSize: 16, fontWeight: 600, color: T.text,
          letterSpacing: '-0.01em', marginBottom: 14 }}>Історія балів</h3>
        <div style={{ background: T.surface, border: `1px solid ${T.border}`,
          borderRadius: T.rLg, overflow: 'hidden' }}>
          {[
            ['+ 120', 'Замовлення #A-2026-04220', '22 квіт 2026', '#16a34a'],
            ['+ 56',  'Замовлення #A-2026-04100', '10 квіт 2026', '#16a34a'],
            ['− 500', 'Списано як знижка',         '08 квіт 2026', '#dc2626'],
            ['+ 280', 'Замовлення #A-2026-03150',  '15 бер 2026',  '#16a34a'],
          ].map(([amount, label, date, color], i, arr) => (
            <div key={i} style={{ padding: '14px 20px',
              borderBottom: i < arr.length-1 ? `1px solid ${T.border}` : 'none',
              display: 'grid', gridTemplateColumns: '80px 1fr auto', gap: 16, alignItems: 'center' }}>
              <div style={{ fontSize: 15, fontWeight: 700, color,
                fontFamily: 'Geist, Inter, sans-serif' }}>{amount}</div>
              <div style={{ fontSize: 13, color: T.text }}>{label}</div>
              <div style={{ fontSize: 12, color: T.text3 }}>{date}</div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

// ── DASHBOARD SUPPORT TICKETS LIST ────────────────────────────────
function DashboardSupportList({ setPage }) {
  const { T } = useContext(ThemeCtx);
  const tickets = [
    { id:'#T-2026-0042', subject:'Питання щодо доставки замовлення',  status:'open',     last:'22 квіт 2026' },
    { id:'#T-2026-0038', subject:'Технічна консультація: диспенсер',    status:'pending',  last:'18 квіт 2026' },
    { id:'#T-2026-0035', subject:'Повернення товару SE50281',           status:'resolved', last:'10 квіт 2026' },
    { id:'#T-2026-0028', subject:'Запит партнерських цін',               status:'resolved', last:'02 квіт 2026' },
  ];

  return (
    <div style={{ background: T.bg, minHeight: 'calc(100vh - 52px)',
      display: 'grid', gridTemplateColumns: '220px 1fr' }}>
      <DashboardSidebar setPage={setPage} active="support-list"/>
      <div style={{ padding: 32 }}>
        <div style={{ display: 'flex', justifyContent: 'space-between',
          alignItems: 'center', marginBottom: 24 }}>
          <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text,
            letterSpacing: '-0.025em',
            fontFamily: 'Geist, Inter, sans-serif' }}>Мої звернення</h2>
          <Btn onClick={()=>setPage('ext-dashboard')}>+ Створити звернення</Btn>
        </div>

        {/* Stats */}
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 12, marginBottom: 24 }}>
          {[
            ['Всього', tickets.length, T.text],
            ['Відкриті', tickets.filter(t=>t.status==='open').length, '#dc2626'],
            ['В обробці', tickets.filter(t=>t.status==='pending').length, '#f59e0b'],
            ['Вирішені', tickets.filter(t=>t.status==='resolved').length, '#16a34a'],
          ].map(([label, count, color], i) => (
            <div key={i} style={{ background: T.surface, border: `1px solid ${T.border}`,
              borderRadius: T.rLg, padding: 20 }}>
              <div style={{ fontSize: 11, fontWeight: 600, color: T.text3,
                textTransform: 'uppercase', letterSpacing: '0.06em', marginBottom: 6 }}>{label}</div>
              <div style={{ fontSize: 28, fontWeight: 800, color,
                letterSpacing: '-0.03em',
                fontFamily: 'Geist, Inter, sans-serif' }}>{count}</div>
            </div>
          ))}
        </div>

        {/* Tickets table */}
        <div style={{ background: T.surface, border: `1px solid ${T.border}`,
          borderRadius: T.rLg, overflow: 'hidden' }}>
          <div style={{ padding: '12px 20px', background: T.bgAlt,
            borderBottom: `1px solid ${T.border}`,
            display: 'grid', gridTemplateColumns: '140px 1fr 120px 120px',
            gap: 16, fontSize: 11, fontWeight: 600, color: T.text3,
            textTransform: 'uppercase', letterSpacing: '0.06em' }}>
            <span>Номер</span><span>Тема</span><span>Статус</span><span>Оновлено</span>
          </div>
          {tickets.map((t, i) => (
            <div key={t.id} style={{ padding: '14px 20px',
              borderBottom: i < tickets.length-1 ? `1px solid ${T.border}` : 'none',
              display: 'grid', gridTemplateColumns: '140px 1fr 120px 120px',
              gap: 16, alignItems: 'center', cursor: 'pointer',
              transition: 'background 0.12s' }}
              onMouseEnter={e => e.currentTarget.style.background = T.muted}
              onMouseLeave={e => e.currentTarget.style.background = 'transparent'}>
              <span style={{ fontSize: 13, fontWeight: 600, color: T.text,
                fontFamily: 'monospace' }}>{t.id}</span>
              <span style={{ fontSize: 13, color: T.text2 }}>{t.subject}</span>
              <span style={{ padding: '3px 10px', fontSize: 11, fontWeight: 500,
                borderRadius: T.rPill, textAlign: 'center', width: 'fit-content',
                background: t.status==='open' ? '#fef2f2' : t.status==='pending' ? '#fffbeb' : '#f0fdf4',
                color: t.status==='open' ? '#dc2626' : t.status==='pending' ? '#b45309' : '#16a34a',
                border: `1px solid ${t.status==='open' ? '#fecaca' : t.status==='pending' ? '#fde68a' : '#bbf7d0'}` }}>
                {t.status==='open' ? 'Відкритий' : t.status==='pending' ? 'В обробці' : 'Вирішений'}
              </span>
              <span style={{ fontSize: 12, color: T.text3 }}>{t.last}</span>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

// ── DASHBOARD SUPPORT TICKET CONVERSATION ────────────────────────
function DashboardSupportThread({ setPage }) {
  const { T } = useContext(ThemeCtx);
  const [msg, setMsg] = useState('');

  return (
    <div style={{ background: T.bg, minHeight: 'calc(100vh - 52px)',
      display: 'grid', gridTemplateColumns: '220px 1fr' }}>
      <DashboardSidebar setPage={setPage} active="support-thread"/>
      <div style={{ padding: 32 }}>
        <div style={{ marginBottom: 16, fontSize: 13, color: T.text2 }}>
          <span onClick={()=>setPage('support-list')} style={{ cursor: 'pointer' }}>← Мої звернення</span>
        </div>

        {/* Header */}
        <div style={{ background: T.surface, border: `1px solid ${T.border}`,
          borderRadius: T.rLg, padding: '20px 24px', marginBottom: 16,
          display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
          <div>
            <div style={{ fontSize: 12, fontWeight: 600, color: T.text3,
              fontFamily: 'monospace', marginBottom: 6 }}>#T-2026-0038</div>
            <h2 style={{ fontSize: 18, fontWeight: 700, color: T.text,
              letterSpacing: '-0.025em',
              fontFamily: 'Geist, Inter, sans-serif' }}>
              Технічна консультація: диспенсер
            </h2>
          </div>
          <span style={{ padding: '4px 12px', fontSize: 11, fontWeight: 500,
            borderRadius: T.rPill, background: '#fffbeb', color: '#b45309',
            border: `1px solid #fde68a` }}>В обробці</span>
        </div>

        {/* Messages */}
        <div style={{ display: 'flex', flexDirection: 'column', gap: 12, marginBottom: 20 }}>
          {[
            { author:'Ви', text:'Доброго дня! Маю питання щодо встановлення сенсорного диспенсера Kimberly-Clark 9960. Чи потрібен спеціальний кронштейн для монтажу на гіпсокартон?', date:'18 квіт 2026, 14:32', isMe:true },
            { author:'Марина (менеджер)', text:'Доброго дня, Іване! Так, для монтажу на гіпсокартон рекомендуємо використовувати спеціальні дюбелі типу "метелик" або молі. Стандартний кронштейн у комплекті, але кріплення потрібне додатково.', date:'18 квіт 2026, 15:10', isMe:false },
            { author:'Ви', text:'Дякую! А ви можете порадити конкретну модель кріплення?', date:'18 квіт 2026, 16:22', isMe:true },
            { author:'Марина (менеджер)', text:'Так, найкраще підійдуть дюбелі Fischer DUOPOWER 8x40. Можемо додати їх до вашого замовлення. Скажіть скільки потрібно і я підготую КП.', date:'19 квіт 2026, 09:15', isMe:false },
          ].map((m, i) => (
            <div key={i} style={{ display: 'flex', justifyContent: m.isMe ? 'flex-end' : 'flex-start' }}>
              <div style={{ maxWidth: '70%', background: m.isMe ? T.invBg : T.surface,
                color: m.isMe ? T.invText : T.text,
                border: `1px solid ${m.isMe ? T.invBg : T.border}`,
                borderRadius: T.rLg, padding: '12px 16px' }}>
                <div style={{ fontSize: 11, fontWeight: 600, marginBottom: 4,
                  opacity: 0.7 }}>{m.author}</div>
                <div style={{ fontSize: 13, lineHeight: 1.6 }}>{m.text}</div>
                <div style={{ fontSize: 10, marginTop: 6, opacity: 0.6 }}>{m.date}</div>
              </div>
            </div>
          ))}
        </div>

        {/* Reply */}
        <div style={{ background: T.surface, border: `1px solid ${T.border}`,
          borderRadius: T.rLg, padding: 16 }}>
          <textarea value={msg} onChange={e=>setMsg(e.target.value)}
            placeholder="Напишіть відповідь..."
            style={{ width: '100%', background: T.bgAlt, border: `1px solid ${T.border}`,
              borderRadius: T.rSm, padding: '10px 12px', color: T.text, fontSize: 14,
              outline: 'none', resize: 'vertical', minHeight: 80,
              fontFamily: 'Geist, Inter, sans-serif', marginBottom: 10 }}/>
          <div style={{ display: 'flex', justifyContent: 'space-between',
            alignItems: 'center' }}>
            <button style={{ background: 'none', border: `1px solid ${T.border}`,
              borderRadius: T.rSm, padding: '7px 14px', color: T.text2,
              fontSize: 13, fontFamily: 'Geist, Inter, sans-serif', cursor: 'pointer',
              display: 'flex', alignItems: 'center', gap: 6 }}>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
              </svg>
              Прикріпити
            </button>
            <Btn disabled={!msg.trim()}>Надіслати</Btn>
          </div>
        </div>
      </div>
    </div>
  );
}

// ── DASHBOARD SIDEBAR (shared) ────────────────────────────────────
function DashboardSidebar({ setPage, active }) {
  const { T } = useContext(ThemeCtx);
  const navItems = [
    { key:'catalog',         icon:'⊞',  label:'Каталог',           page:'ext-dashboard' },
    { key:'orders',          icon:'◫',  label:'Замовлення',         page:'ext-dashboard' },
    { key:'drop',            icon:'📦', label:'Дропшипінг',         page:'ext-dashboard' },
    { key:'documents',       icon:'⊟',  label:'Документи',         page:'ext-dashboard' },
    { key:'rules',           icon:'◈',  label:'Знижки',             page:'ext-dashboard' },
    { key:'rules-program',   icon:'🏆', label:'Програма лояльності', page:'rules-program' },
    { key:'profile',         icon:'◎',  label:'Профіль',           page:'ext-dashboard' },
    { key:'support',         icon:'◉',  label:'Підтримка',         page:'ext-dashboard' },
    { key:'support-list',    icon:'💬', label:'Мої звернення',     page:'support-list' },
  ];

  return (
    <div style={{ background: T.surface, borderRight: `1px solid ${T.border}`,
      padding: '20px 0', display: 'flex', flexDirection: 'column' }}>
      <div style={{ padding: '0 16px 20px', borderBottom: `1px solid ${T.border}`, marginBottom: 8 }}>
        <div style={{ width: 44, height: 44, background: T.muted, borderRadius: T.rLg,
          display: 'flex', alignItems: 'center', justifyContent: 'center',
          marginBottom: 10, fontSize: 20 }}>👤</div>
        <div style={{ fontSize: 13, fontWeight: 600, color: T.text }}>ТОВ "Авто Сервіс"</div>
        <div style={{ fontSize: 11, color: T.text3, marginTop: 2 }}>Bronze · Gold partner</div>
      </div>
      <div style={{ flex: 1 }}>
        {navItems.map(item => (
          <div key={item.key} onClick={()=>setPage(item.page)}
            style={{ padding: '9px 16px', fontSize: 14, cursor: 'pointer',
              fontWeight: active===item.key ? 500 : 400,
              color: active===item.key ? T.text : T.text2,
              background: active===item.key ? T.muted : 'transparent',
              borderLeft: `2px solid ${active===item.key ? T.invBg : 'transparent'}`,
              transition: 'all 0.12s', display: 'flex', alignItems: 'center', gap: 10 }}>
            <span style={{ fontSize: 15 }}>{item.icon}</span>{item.label}
          </div>
        ))}
      </div>
      <div style={{ padding: '12px 16px', borderTop: `1px solid ${T.border}` }}>
        <span onClick={()=>setPage('main')} style={{ fontSize: 13, color: T.text3, cursor: 'pointer' }}>
          ← Магазин
        </span>
      </div>
    </div>
  );
}

// ── ROLE SELECTION VARIATION (з обраним кабінетом) ───────────────
function RoleSelectionConfirmedPage({ setPage }) {
  const { T } = useContext(ThemeCtx);

  return (
    <div style={{ background: T.bg, minHeight: '80vh', display: 'flex',
      flexDirection: 'column', alignItems: 'center', justifyContent: 'center',
      padding: '60px 60px', textAlign: 'center' }}>
      <div style={{ width: 72, height: 72, background: T.bg==='#09090b'?'#052e16':'#f0fdf4',
        border: `1px solid ${T.bg==='#09090b'?'#14532d':'#bbf7d0'}`,
        borderRadius: T.rLg, display: 'flex', alignItems: 'center',
        justifyContent: 'center', marginBottom: 24 }}>
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
          stroke="#16a34a" strokeWidth="2.5">
          <polyline points="20 6 9 17 4 12"/>
        </svg>
      </div>
      <h1 style={{ fontSize: 28, fontWeight: 700, color: T.text,
        letterSpacing: '-0.025em', marginBottom: 12,
        fontFamily: 'Geist, Inter, sans-serif' }}>
        Ваш кабінет налаштовано!
      </h1>
      <p style={{ fontSize: 15, color: T.text2, lineHeight: 1.65, marginBottom: 32, maxWidth: 480 }}>
        Ми отримали ваш запит на створення <strong style={{ color: T.text }}>Бізнес-кабінету</strong>.
        Менеджер зв'яжеться з вами протягом одного робочого дня для верифікації даних
        та надання повного доступу.
      </p>

      <div style={{ background: T.surface, border: `1px solid ${T.border}`,
        borderRadius: T.rLg, padding: 24, marginBottom: 24, maxWidth: 480, width: '100%' }}>
        <div style={{ fontSize: 11, fontWeight: 600, color: T.text3,
          textTransform: 'uppercase', letterSpacing: '0.08em', marginBottom: 12,
          textAlign: 'left' }}>Що далі</div>
        {[
          ['1', 'Менеджер зателефонує для уточнення деталей'],
          ['2', 'Підпишемо договір (надішлемо на email)'],
          ['3', 'Активуємо партнерські ціни та знижки'],
          ['4', 'Призначимо персонального менеджера'],
        ].map(([n, t]) => (
          <div key={n} style={{ display: 'flex', gap: 12, alignItems: 'center',
            padding: '8px 0', textAlign: 'left' }}>
            <div style={{ width: 24, height: 24, borderRadius: '50%', background: T.muted,
              border: `1px solid ${T.border}`, display: 'flex', alignItems: 'center',
              justifyContent: 'center', fontSize: 12, fontWeight: 700, color: T.text2,
              flexShrink: 0, fontFamily: 'Geist, Inter, sans-serif' }}>{n}</div>
            <span style={{ fontSize: 13, color: T.text2 }}>{t}</span>
          </div>
        ))}
      </div>

      <div style={{ display: 'flex', gap: 12 }}>
        <Btn onClick={()=>setPage('main')}>На головну</Btn>
        <Btn variant="outline" onClick={()=>setPage('catalog')}>До каталогу</Btn>
      </div>
    </div>
  );
}

Object.assign(window, {
  DashboardRulesProgram, DashboardSupportList, DashboardSupportThread,
  DashboardSidebar, RoleSelectionConfirmedPage,
});
