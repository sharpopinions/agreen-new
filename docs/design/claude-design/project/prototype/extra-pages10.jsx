// ── EXTRA PAGES 10 ────────────────────────────────────────────────
// Favorite Pop-up (hover dropdown on wishlist icon)
// Compare-Glue is handled inside ComparePage via category switcher

// ── FAVORITE POPUP (hover on wishlist icon in header) ────────────
function FavoritePopup({ items, setPage, onRemove, onClose }) {
  const { T } = useContext(ThemeCtx);

  if (items.length === 0) {
    return (
      <div onMouseEnter={() => {}} onMouseLeave={onClose}
        style={{ position: 'absolute', top: 'calc(100% + 4px)', right: 0,
          width: 360, background: T.surface, border: `1px solid ${T.border}`,
          borderRadius: T.rLg, boxShadow: T.shadowMd,
          padding: 32, textAlign: 'center', zIndex: 800 }}>
        <div style={{ width: 56, height: 56, background: T.muted, borderRadius: T.rLg,
          display: 'flex', alignItems: 'center', justifyContent: 'center',
          margin: '0 auto 14px' }}>
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke={T.text3} strokeWidth="1.5">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
          </svg>
        </div>
        <div style={{ fontSize: 14, fontWeight: 600, color: T.text, marginBottom: 4 }}>
          Список бажань порожній
        </div>
        <div style={{ fontSize: 12, color: T.text2, marginBottom: 16, lineHeight: 1.5 }}>
          Додайте товари, щоб не загубити їх
        </div>
        <Btn size="sm" onClick={() => { onClose(); setPage('catalog'); }}>До каталогу</Btn>
      </div>
    );
  }

  return (
    <div onMouseEnter={() => {}} onMouseLeave={onClose}
      style={{ position: 'absolute', top: 'calc(100% + 4px)', right: 0,
        width: 360, background: T.surface, border: `1px solid ${T.border}`,
        borderRadius: T.rLg, boxShadow: T.shadowMd,
        overflow: 'hidden', zIndex: 800 }}>
      {/* Header */}
      <div style={{ padding: '14px 16px', borderBottom: `1px solid ${T.border}`,
        display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <div style={{ fontSize: 13, fontWeight: 600, color: T.text }}>
          Список бажань · {items.length}
        </div>
        <span onClick={() => { onClose(); setPage('favorites'); }}
          style={{ fontSize: 11, color: T.text2, cursor: 'pointer',
            textDecoration: 'underline', textUnderlineOffset: 2 }}>
          Усі товари →
        </span>
      </div>

      {/* Items */}
      <div style={{ maxHeight: 320, overflowY: 'auto' }}>
        {items.slice(0, 4).map(p => (
          <div key={p.id} style={{ padding: '12px 16px',
            borderBottom: `1px solid ${T.border}`, display: 'flex', gap: 12,
            alignItems: 'center', cursor: 'pointer', transition: 'background 0.12s' }}
            onMouseEnter={e => e.currentTarget.style.background = T.muted}
            onMouseLeave={e => e.currentTarget.style.background = 'transparent'}>
            <div style={{ width: 48, height: 48, flexShrink: 0, borderRadius: T.rSm,
              overflow: 'hidden' }}>
              <Img h={48} label=""/>
            </div>
            <div style={{ flex: 1, minWidth: 0 }} onClick={() => { onClose(); setPage('product'); }}>
              <div style={{ fontSize: 12, color: T.text, lineHeight: 1.4,
                overflow: 'hidden', textOverflow: 'ellipsis',
                display: '-webkit-box', WebkitLineClamp: 2, WebkitBoxOrient: 'vertical' }}>
                {p.name}
              </div>
              <div style={{ fontSize: 13, fontWeight: 700, color: T.text,
                marginTop: 4, letterSpacing: '-0.02em' }}>
                {fmt(p.price)}
              </div>
            </div>
            <button onClick={() => onRemove(p.id)}
              style={{ background: 'none', border: 'none', cursor: 'pointer',
                color: T.text3, padding: 6, borderRadius: T.rSm,
                transition: 'color 0.12s' }}
              onMouseEnter={e => e.currentTarget.style.color = T.danger}
              onMouseLeave={e => e.currentTarget.style.color = T.text3}>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" strokeWidth="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>
        ))}
        {items.length > 4 && (
          <div style={{ padding: '10px 16px', textAlign: 'center', fontSize: 11,
            color: T.text3, borderBottom: `1px solid ${T.border}` }}>
            та ще {items.length - 4}...
          </div>
        )}
      </div>

      {/* Footer */}
      <div style={{ padding: 12, background: T.bgAlt }}>
        <Btn full size="sm" onClick={() => { onClose(); setPage('favorites'); }}>
          Переглянути всі ({items.length})
        </Btn>
      </div>
    </div>
  );
}

Object.assign(window, { FavoritePopup });
