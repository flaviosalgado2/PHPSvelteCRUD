/**
 * Chart data configurations
 */

export const revenueChartData = {
  labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
  datasets: [
    {
      label: 'Receita 2026',
      data: [12000, 19000, 15000, 25000, 22000, 30000, 28000, 32000, 29000, 35000, 38000, 42000],
      borderColor: 'rgb(59, 130, 246)',
      backgroundColor: 'rgba(59, 130, 246, 0.1)',
      tension: 0.4,
      fill: true
    },
    {
      label: 'Receita 2025',
      data: [10000, 15000, 13000, 20000, 18000, 25000, 23000, 27000, 24000, 30000, 32000, 35000],
      borderColor: 'rgb(156, 163, 175)',
      backgroundColor: 'rgba(156, 163, 175, 0.1)',
      tension: 0.4,
      fill: true
    }
  ]
};

export const salesChartData = {
  labels: ['Produto A', 'Produto B', 'Produto C', 'Produto D', 'Produto E'],
  datasets: [
    {
      label: 'Vendas',
      data: [300, 450, 200, 500, 350],
      backgroundColor: [
        'rgba(59, 130, 246, 0.8)',
        'rgba(16, 185, 129, 0.8)',
        'rgba(251, 146, 60, 0.8)',
        'rgba(239, 68, 68, 0.8)',
        'rgba(168, 85, 247, 0.8)'
      ],
      borderWidth: 0
    }
  ]
};

export const userGrowthData = {
  labels: ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'],
  datasets: [
    {
      label: 'Novos Usuários',
      data: [65, 85, 120, 95],
      backgroundColor: 'rgba(59, 130, 246, 0.8)',
      borderColor: 'rgb(59, 130, 246)',
      borderWidth: 2
    }
  ]
};
