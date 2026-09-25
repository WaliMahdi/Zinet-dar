import api from './api'

export const panierService = {
  /**
   * GET /api/panier
   */
  async get() {
    const res = await api.get('/panier')
    return res.data
  },

  /**
   * POST /api/panier/ajouter
   * Body: { produit_id, quantite }
   */
  async ajouter(produit_id, quantite = 1) {
    const res = await api.post('/panier/ajouter', { produit_id, quantite })
    return res.data
  },

  /**
   * PATCH /api/panier/{produit}/quantite
   * Body: { quantite }
   */
  async updateQuantite(produitId, quantite) {
    const res = await api.patch(`/panier/${produitId}/quantite`, { quantite })
    return res.data
  },

  /**
   * DELETE /api/panier/{produit}
   */
  async retirer(produitId) {
    const res = await api.delete(`/panier/${produitId}`)
    return res.data
  },

  /**
   * DELETE /api/panier/vider
   */
  async vider() {
    const res = await api.delete('/panier/vider')
    return res.data
  },

  /**
   * POST /api/panier/commander
   * Body: { nom_client, telephone, adresse, note_client? }
   */
  async commander(data) {
    const res = await api.post('/panier/commander', {
      nom_client:  data.nom_client,
      telephone:   data.telephone,
      adresse:     data.adresse,
      note_client: data.note_client || undefined,
    })
    return res.data
  },
}
