// ── COMMAND PALETTE (Cmd+K) ───────────────────────────────────────
function CommandPalette({ open, onClose, setPage }) {
  const { T } = useContext(ThemeCtx);
  const [query, setQuery] = useState('');
  const [selectedIdx, setSelectedIdx] = useState(0);
  const inputRef = useRef(null);

  useEffect(() => {
    if (open) {
      setTimeout(() => inputRef.current?.focus(), 50);
      setQuery('');
      setSelectedIdx(0);
    }
  }, [open]);

  // Build searchable index
  const allItems = [
    // Pages
    { kind:'Сторінка', name:'Головна',         icon:'🏠', go:'main'           },
    { kind:'Сторінка', name:'Каталог',         icon:'⊞',  go:'catalog'        },
    { kind:'Сторінка', name:'Бренди',          icon:'🏷️', go:'brands'         },
    { kind:'Сторінка', name:'Послуги',         icon:'⚙️', go:'services'       },
    { kind:'Сторінка', name:'Партнерам',       icon:'🤝', go:'partners-popup' },
    { kind:'Сторінка', name:'Про нас',         icon:'ℹ️', go:'about'          },
    { kind:'Сторінка', name:'Блог',            icon:'📰', go:'blog'           },
    { kind:'Сторінка', name:'Контакти',        icon:'📞', go:'contacts'       },
    { kind:'Сторінка', name:'Кошик',           icon:'🛒', go:'cart'           },
    { kind:'Сторінка', name:'Обрані товари',   icon:'❤️', go:'favorites'      },
    { kind:'Сторінка', name:'Порівняння',      icon:'⚖️', go:'compare'        },
    { kind:'Сторінка', name:'Доставка',        icon:'🚚', go:'delivery'       },
    { kind:'Сторінка', name:'Особистий кабінет',icon:'👤',go:'ext-dashboard'  },
    { kind:'Сторінка', name:'Вакансії',        icon:'💼', go:'vacancies'      },
    { kind:'Сторінка', name:'Навчальний центр',icon:'🎓', go:'study-center'   },
    { kind:'Сторінка', name:'Акції',           icon:'🔥', go:'aktsyii'        },
    // Categories
    ...CATEGORIES.map(c => ({ kind:'Категорія', name:c.name, icon:'📦', go:'catalog' })),
    // Brands
    ...BRANDS.map(b => ({ kind:'Бренд', name:b, icon:'🏭', go:'brand-products' })),
    // Products
    ...PRODUCTS.map(p => ({ kind:'Товар', name:p.name, sub:p.sku + ' · ' + fmt(p.price), icon:'🔧', go:'product' })),
    // Actions
    { kind:'Дія', name:'Перемкнути тему',   icon:'🌗', action:'toggle-theme' },
    { kind:'Дія', name:'Сторінка 404',       icon:'🚧', go:'404' },
    { kind:'Дія', name:'Tweaks (панель)',    icon:'⚡', action:'tweaks' },
  ];

  const filtered = query
    ? allItems.filter(i =>
        i.name.toLowerCase().includes(query.toLowerCase()) ||
        i.kind.toLowerCase().includes(query.toLowerCase())
      )
    : allItems.slice(0, 12);

  useEffect(() => { setSelectedIdx(0); }, [query]);

  const handleSelect = (item) => {
    if (item.go) setPage(item.go);
    if (item.action === 'toggle-theme') {
      const dark = localStorage.getItem('ag_dark') === 'true';
      localStorage.setItem('ag_dark', !dark);
      window.location.reload();
    }
    onClose();
  };

  const handleKeyDown = (e) => {
    if (e.key === 'Escape') { onClose(); return; }
    if (e.key === 'ArrowDown') { e.preventDefault(); setSelectedIdx(i => Math.min(filtered.length-1, i+1)); }
    if (e.key === 'ArrowUp')   { e.preventDefault(); setSelectedIdx(i => Math.max(0, i-1)); }
    if (e.key === 'Enter') {
      e.preventDefault();
      if (filtered[selectedIdx]) handleSelect(filtered[selectedIdx]);
    }
  };

  // Group results by kind
  const groups = {};
  filtered.forEach((item, idx) => {
    if (!groups[item.kind]) groups[item.kind] = [];
    groups[item.kind].push({ ...item, globalIdx: idx });
  });

  if (!open) return null;

  return (
    <div onClick={e => { if (e.target === e.currentTarget) onClose(); }}
      style={{ position:'fixed', inset:0, background:'rgba(0,0,0,0.5)',
        backdropFilter:'blur(4px)', zIndex:9500, display:'flex',
        alignItems:'flex-start', justifyContent:'center', paddingTop:80,
        animation:'cmdkFade 0.15s ease' }}>
      <style>{`
        @keyframes cmdkFade { from{opacity:0} to{opacity:1} }
        @keyframes cmdkSlide { from{transform:translateY(-12px);opacity:0} to{transform:translateY(0);opacity:1} }
      `}</style>
      <div style={{ width:640, maxWidth:'90vw', background:T.surface,
        border:`1px solid ${T.border}`, borderRadius:T.rLg,
        boxShadow:'0 24px 64px rgba(0,0,0,0.4)', overflow:'hidden',
        animation:'cmdkSlide 0.2s cubic-bezier(0.16,1,0.3,1)' }}>

        {/* Search input */}
        <div style={{ padding:'14px 16px', borderBottom:`1px solid ${T.border}`,
          display:'flex', alignItems:'center', gap:12 }}>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
            stroke={T.text3} strokeWidth="2" style={{ flexShrink:0 }}>
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
          </svg>
          <input ref={inputRef} value={query} onChange={e=>setQuery(e.target.value)}
            onKeyDown={handleKeyDown}
            placeholder="Пошук сторінок, товарів, брендів..."
            style={{ flex:1, background:'transparent', border:'none', outline:'none',
              color:T.text, fontSize:15, fontFamily:'Geist, Inter, sans-serif',
              fontWeight:400 }}/>
          <span style={{ padding:'2px 8px', background:T.muted, border:`1px solid ${T.border}`,
            borderRadius:T.rSm, fontSize:11, fontWeight:500, color:T.text3,
            fontFamily:'monospace' }}>ESC</span>
        </div>

        {/* Results */}
        <div style={{ maxHeight: 420, overflowY: 'auto', padding: '8px 0' }}>
          {filtered.length === 0 ? (
            <div style={{ padding: '40px 20px', textAlign: 'center', color: T.text3 }}>
              <div style={{ fontSize: 13, marginBottom: 6 }}>Нічого не знайдено</div>
              <div style={{ fontSize: 11 }}>Спробуйте інші ключові слова</div>
            </div>
          ) : (
            Object.entries(groups).map(([kind, items]) => (
              <div key={kind}>
                <div style={{ padding: '8px 16px 4px', fontSize: 10, fontWeight: 600,
                  letterSpacing: '0.1em', textTransform: 'uppercase', color: T.text3 }}>
                  {kind}
                </div>
                {items.map(item => {
                  const sel = item.globalIdx === selectedIdx;
                  return (
                    <div key={item.globalIdx}
                      onClick={() => handleSelect(item)}
                      onMouseEnter={() => setSelectedIdx(item.globalIdx)}
                      style={{ padding: '8px 16px', display: 'flex', alignItems: 'center',
                        gap: 12, cursor: 'pointer',
                        background: sel ? T.muted : 'transparent',
                        borderLeft: `2px solid ${sel ? T.invBg : 'transparent'}`,
                        transition: 'background 0.05s' }}>
                      <span style={{ fontSize: 16, width: 20, textAlign: 'center', flexShrink: 0 }}>
                        {item.icon}
                      </span>
                      <div style={{ flex: 1, minWidth: 0 }}>
                        <div style={{ fontSize: 13, color: T.text, fontWeight: 500,
                          overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
                          {item.name}
                        </div>
                        {item.sub && (
                          <div style={{ fontSize: 11, color: T.text3, marginTop: 2 }}>{item.sub}</div>
                        )}
                      </div>
                      {sel && (
                        <span style={{ fontSize: 10, color: T.text3, fontFamily: 'monospace' }}>↵</span>
                      )}
                    </div>
                  );
                })}
              </div>
            ))
          )}
        </div>

        {/* Footer */}
        <div style={{ padding: '10px 16px', background: T.bgAlt,
          borderTop: `1px solid ${T.border}`, display: 'flex',
          justifyContent: 'space-between', alignItems: 'center',
          fontSize: 11, color: T.text3 }}>
          <div style={{ display: 'flex', gap: 16 }}>
            <span><kbd style={kbdStyle(T)}>↑</kbd> <kbd style={kbdStyle(T)}>↓</kbd> навігація</span>
            <span><kbd style={kbdStyle(T)}>↵</kbd> вибрати</span>
            <span><kbd style={kbdStyle(T)}>ESC</kbd> закрити</span>
          </div>
          <span>{filtered.length} результатів</span>
        </div>
      </div>
    </div>
  );
}

const kbdStyle = (T) => ({
  display: 'inline-block', padding: '0 5px', minWidth: 16, height: 16,
  background: T.surface, border: `1px solid ${T.border}`, borderRadius: 3,
  fontSize: 10, fontWeight: 500, color: T.text2,
  fontFamily: 'monospace', textAlign: 'center', lineHeight: '14px',
  marginRight: 4,
});

Object.assign(window, { CommandPalette });
