/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

import { useState, useMemo, ChangeEvent } from 'react';
import {
  Folder,
  File,
  ChevronDown,
  ChevronRight,
  Search,
  BookOpen,
  Cpu,
  Globe,
  Database,
  Server,
  CheckCircle2,
  Code,
  AlertCircle,
  Copy,
  Check,
  BookMarked,
  Info,
  Layers,
  ArrowRight,
  Settings,
  HelpCircle,
  Terminal,
  FileJson
} from 'lucide-react';
import { motion, AnimatePresence } from 'motion/react';
import { FileNode } from './types';
import { FILE_TREE_DATA, STATIC_FILE_CONTENTS, PROJECT_STATS } from './data';

export default function App() {
  const [selectedPath, setSelectedPath] = useState<string>('README.md');
  const [expandedNodes, setExpandedNodes] = useState<Record<string, boolean>>({
    'CosmicLib Workspace': true,
    'docs': true,
    'blueprint': true,
    '.github': false,
    'prompts': false,
    'examples': true,
  });
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [copied, setCopied] = useState<boolean>(false);
  const [viewMode, setViewMode] = useState<'formatted' | 'raw'>('formatted');

  // Toggle directory expand/collapse
  const toggleNode = (nodeName: string) => {
    setExpandedNodes(prev => ({
      ...prev,
      [nodeName]: !prev[nodeName]
    }));
  };

  // Safe file content resolver
  const activeContent = useMemo(() => {
    return STATIC_FILE_CONTENTS[selectedPath] || `# ${selectedPath}\n\nContent file ini telah berhasil diinisialisasi di dalam repository lokal.`;
  }, [selectedPath]);

  // Handle Search filtering
  const handleSearchChange = (e: ChangeEvent<HTMLInputElement>) => {
    setSearchQuery(e.target.value);
  };

  const handleCopy = () => {
    navigator.clipboard.writeText(activeContent);
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  // Recursive File Tree Node Renderer
  const renderFileTree = (node: FileNode, depth = 0) => {
    const isDirectory = node.type === 'directory';
    const isExpanded = expandedNodes[node.name];
    const isSelected = selectedPath === node.path;

    // Filter nodes in search if query is active
    if (searchQuery) {
      const matchSelf = node.name.toLowerCase().includes(searchQuery.toLowerCase()) || node.path.toLowerCase().includes(searchQuery.toLowerCase());
      const hasMatchingChildren = isDirectory && node.children?.some(child => 
        child.name.toLowerCase().includes(searchQuery.toLowerCase()) || child.path.toLowerCase().includes(searchQuery.toLowerCase())
      );
      if (!matchSelf && !hasMatchingChildren) {
        return null;
      }
    }

    return (
      <div key={node.path} className="select-none">
        <div
          id={`tree-node-${node.name.replace(/\s+/g, '-').toLowerCase()}`}
          onClick={() => {
            if (isDirectory) {
              toggleNode(node.name);
            } else {
              setSelectedPath(node.path);
            }
          }}
          className={`flex items-center gap-2 py-1.5 px-2 rounded-lg cursor-pointer transition-all duration-200 text-xs my-0.5 ${
            isSelected 
              ? 'bg-cosmic-purple/25 text-cosmic-cyan border-l-2 border-cosmic-cyan font-medium pl-2.5' 
              : 'text-space-300 hover:bg-space-800/60 hover:text-white'
          }`}
          style={{ paddingLeft: `${Math.max(8, depth * 12)}px` }}
        >
          {isDirectory ? (
            <>
              <span className="text-space-500">
                {isExpanded ? <ChevronDown size={14} /> : <ChevronRight size={14} />}
              </span>
              <Folder size={14} className={isExpanded ? "text-cosmic-purple" : "text-space-400"} />
              <span className="truncate font-medium">{node.name}</span>
            </>
          ) : (
            <>
              <span className="w-3.5" />
              {node.name.endsWith('.sql') ? (
                <Database size={13} className="text-cosmic-cyan" />
              ) : node.name.endsWith('.json') ? (
                <FileJson size={13} className="text-cosmic-teal" />
              ) : node.path.startsWith('docs/') ? (
                <BookOpen size={13} className="text-cosmic-pink" />
              ) : (
                <File size={13} className="text-space-400" />
              )}
              <span className="truncate">{node.name}</span>
            </>
          )}
        </div>

        {isDirectory && isExpanded && node.children && (
          <div className="border-l border-space-800/40 ml-2.5">
            {node.children.map(child => renderFileTree(child, depth + 1))}
          </div>
        )}
      </div>
    );
  };

  // Simple, elegant Markdown visual parser for high fidelity document viewer
  const renderMarkdownContent = (text: string) => {
    const lines = text.split('\n');
    let inCodeBlock = false;
    let codeBlockContent: string[] = [];

    return lines.map((line, idx) => {
      // Code block toggles
      if (line.startsWith('```')) {
        if (inCodeBlock) {
          inCodeBlock = false;
          const content = codeBlockContent.join('\n');
          codeBlockContent = [];
          return (
            <div key={idx} className="my-4 font-mono text-[11px] leading-relaxed bg-space-950 border border-space-800/60 rounded-xl overflow-hidden shadow-2xl">
              <div className="flex items-center justify-between px-4 py-2 bg-space-900 border-b border-space-800/40 text-space-400">
                <span className="flex items-center gap-1.5 font-medium"><Terminal size={12} className="text-cosmic-cyan" /> SINTAKS KODE</span>
                <span className="text-[10px]">MYSQL / PHP</span>
              </div>
              <pre className="p-4 overflow-x-auto text-cyan-300/90 whitespace-pre scrollbar-thin"><code>{content}</code></pre>
            </div>
          );
        } else {
          inCodeBlock = true;
          return null;
        }
      }

      if (inCodeBlock) {
        codeBlockContent.push(line);
        return null;
      }

      // Headers
      if (line.startsWith('# ')) {
        return (
          <h1 key={idx} className="text-2xl font-display font-bold text-white tracking-tight mt-6 mb-4 border-b border-space-800/50 pb-2 flex items-center gap-2">
            <span className="w-1.5 h-6 bg-cosmic-cyan rounded-full inline-block" />
            {line.replace('# ', '')}
          </h1>
        );
      }
      if (line.startsWith('## ')) {
        return (
          <h2 key={idx} className="text-lg font-display font-semibold text-cosmic-purple tracking-tight mt-6 mb-3 flex items-center gap-2">
            {line.replace('## ', '')}
          </h2>
        );
      }
      if (line.startsWith('### ')) {
        return (
          <h3 key={idx} className="text-sm font-sans font-semibold text-white/95 mt-4 mb-2 flex items-center gap-1.5">
            <span className="w-1 h-3 bg-cosmic-pink rounded-full inline-block" />
            {line.replace('### ', '')}
          </h3>
        );
      }

      // Blockquotes
      if (line.startsWith('> ')) {
        return (
          <blockquote key={idx} className="border-l-4 border-cosmic-cyan bg-cosmic-cyan/5 text-space-200 px-4 py-3 rounded-r-xl my-4 text-xs italic leading-relaxed">
            {line.replace('> ', '')}
          </blockquote>
        );
      }

      // Horizontal lines
      if (line === '---') {
        return <hr key={idx} className="border-space-800/50 my-6" />;
      }

      // Lists
      if (line.startsWith('- [ ] ') || line.startsWith('- [x] ')) {
        const checked = line.startsWith('- [x] ');
        return (
          <div key={idx} className="flex items-center gap-2.5 py-1 text-xs">
            <div className={`w-4 h-4 rounded-md border flex items-center justify-center shrink-0 ${checked ? 'bg-cosmic-teal/20 border-cosmic-teal text-cosmic-teal' : 'border-space-600 bg-space-900'}`}>
              {checked && <Check size={10} strokeWidth={3} />}
            </div>
            <span className={checked ? 'text-space-500 line-through' : 'text-space-200'}>
              {line.substring(6)}
            </span>
          </div>
        );
      }

      if (line.startsWith('- ')) {
        return (
          <li key={idx} className="list-none flex items-start gap-2 py-1 text-xs text-space-300 leading-relaxed ml-2">
            <span className="text-cosmic-cyan mt-1.5 shrink-0 w-1.5 h-1.5 rounded-full bg-cosmic-cyan" />
            <span>{line.replace('- ', '')}</span>
          </li>
        );
      }

      if (/^\d+\.\s/.test(line)) {
        const num = line.match(/^\d+/)?.[0];
        const content = line.replace(/^\d+\.\s/, '');
        return (
          <div key={idx} className="flex items-start gap-2 py-1 text-xs text-space-300 leading-relaxed ml-2">
            <span className="font-mono font-bold text-cosmic-purple shrink-0 min-w-4">{num}.</span>
            <span>{content}</span>
          </div>
        );
      }

      // Empty line
      if (line.trim() === '') {
        return <div key={idx} className="h-2" />;
      }

      // Table formatting
      if (line.startsWith('|') && idx > 0 && lines[idx - 1].startsWith('|')) {
        if (line.includes('---')) return null; // skip divider
        const cells = line.split('|').map(c => c.trim()).filter(Boolean);
        return (
          <div key={idx} className="overflow-x-auto my-2 border border-space-800/50 rounded-xl bg-space-900/40 p-1">
            <table className="w-full text-xs text-left">
              <tbody>
                <tr className="hover:bg-space-800/20">
                  {cells.map((cell, cidx) => (
                    <td key={cidx} className={`p-2.5 ${cidx === 0 ? 'font-medium text-white min-w-32' : 'text-space-300'}`}>
                      {cell}
                    </td>
                  ))}
                </tr>
              </tbody>
            </table>
          </div>
        );
      }

      // Default paragraph (supports inline bolding **)
      const parseBoldText = (txt: string) => {
        const parts = txt.split('**');
        return parts.map((part, i) => i % 2 === 1 ? <strong key={i} className="text-white font-semibold">{part}</strong> : part);
      };

      return (
        <p key={idx} className="text-xs text-space-300 leading-relaxed my-1.5">
          {parseBoldText(line)}
        </p>
      );
    });
  };

  return (
    <div className="min-h-screen bg-space-950 text-white font-sans flex flex-col selection:bg-cosmic-purple selection:text-white">
      {/* 🌌 High-Fidelity Header */}
      <header className="border-b border-space-800/40 bg-space-900/60 backdrop-blur-xl px-6 py-4 flex items-center justify-between shrink-0 sticky top-0 z-50">
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-tr from-cosmic-purple via-cosmic-blue to-cosmic-cyan flex items-center justify-center shadow-lg shadow-cosmic-purple/20">
            <Layers size={20} className="text-white" />
          </div>
          <div>
            <div className="flex items-center gap-2">
              <h1 className="text-base font-display font-bold tracking-wide text-white">CosmicLib Engine</h1>
              <span className="text-[10px] px-2 py-0.5 bg-cosmic-purple/15 text-cosmic-cyan border border-cosmic-cyan/30 rounded-full font-mono">Fase 1: Cetak Biru</span>
            </div>
            <p className="text-[10.5px] text-space-400">Pondasi Sistem & Repositori Dokumentasi • target Laravel 12 & MySQL</p>
          </div>
        </div>

        <div className="flex items-center gap-3">
          <div className="hidden md:flex items-center gap-1.5 text-xs text-space-400 bg-space-950 border border-space-800/50 px-3 py-1.5 rounded-xl">
            <span className="w-2 h-2 rounded-full bg-cosmic-teal animate-pulse" />
            <span>Workspace Siap Dikembangkan</span>
          </div>
          <button 
            id="help-btn"
            onClick={() => setSelectedPath('PROJECT_MANIFEST.md')}
            className="p-2 text-space-400 hover:text-white hover:bg-space-800 rounded-lg transition-colors"
            title="Manifes Proyek"
          >
            <Info size={18} />
          </button>
        </div>
      </header>

      {/* 📊 Core Statistics Cards */}
      <section className="bg-space-900/20 border-b border-space-800/30 px-6 py-4 grid grid-cols-2 lg:grid-cols-4 gap-4 shrink-0">
        {PROJECT_STATS.map((stat, i) => (
          <div key={i} className="bg-space-900/40 border border-space-800/40 rounded-2xl p-3 flex items-start gap-3 transition-all hover:border-space-700/60">
            <div className="p-2 bg-space-800/80 rounded-xl text-cosmic-cyan">
              {stat.icon === 'Cpu' && <Cpu size={16} />}
              {stat.icon === 'Globe' && <Globe size={16} />}
              {stat.icon === 'Database' && <Database size={16} />}
              {stat.icon === 'Server' && <Server size={16} />}
            </div>
            <div>
              <span className="text-[10px] text-space-500 uppercase tracking-wider font-semibold block">{stat.label}</span>
              <span className="text-xs font-bold text-white block mt-0.5">{stat.value}</span>
              <span className="text-[10px] text-space-400 block mt-0.5">{stat.description}</span>
            </div>
          </div>
        ))}
      </section>

      {/* Main Panel */}
      <div className="flex-1 flex overflow-hidden">
        {/* 🗂️ Left Sidebar: File Tree & Navigation */}
        <aside className="w-80 border-r border-space-800/40 bg-space-900/30 p-4 flex flex-col gap-4 overflow-y-auto shrink-0 scrollbar-thin">
          {/* Quick Search */}
          <div className="relative">
            <Search className="absolute left-3 top-2.5 text-space-500" size={14} />
            <input
              id="search-input"
              type="text"
              placeholder="Cari file atau cetak biru..."
              value={searchQuery}
              onChange={handleSearchChange}
              className="w-full bg-space-950 border border-space-800 text-xs text-white pl-9 pr-3 py-2 rounded-xl focus:outline-none focus:border-cosmic-purple transition-all"
            />
          </div>

          {/* Quick Navigation Items */}
          <div>
            <span className="text-[10px] text-space-500 font-bold uppercase tracking-wider block mb-2 px-1">Dokumen Prioritas</span>
            <div className="space-y-1">
              {[
                { path: 'README.md', label: 'Selamat Datang (README)', icon: BookMarked, color: 'text-cosmic-cyan' },
                { path: 'PROJECT_MANIFEST.md', label: 'Project Manifest', icon: Layers, color: 'text-cosmic-purple' },
                { path: 'ROADMAP.md', label: 'Roadmap Utama', icon: ChevronRight, color: 'text-cosmic-pink' },
                { path: 'blueprint/database_schema.sql', label: 'Skema Database SQL', icon: Database, color: 'text-cosmic-cyan' }
              ].map((item) => {
                const isSelected = selectedPath === item.path;
                return (
                  <button
                    key={item.path}
                    id={`quick-nav-${item.path.replace(/\//g, '-').replace(/\./g, '-')}`}
                    onClick={() => setSelectedPath(item.path)}
                    className={`w-full flex items-center justify-between text-left text-xs py-2 px-3 rounded-xl transition-all duration-200 ${
                      isSelected 
                        ? 'bg-cosmic-purple/15 border border-cosmic-purple/40 text-white font-medium shadow-md shadow-cosmic-purple/5' 
                        : 'text-space-300 hover:bg-space-800/40 hover:text-white border border-transparent'
                    }`}
                  >
                    <div className="flex items-center gap-2 truncate">
                      <item.icon size={13} className={item.color} />
                      <span className="truncate">{item.label}</span>
                    </div>
                    {isSelected && <div className="w-1.5 h-1.5 rounded-full bg-cosmic-cyan" />}
                  </button>
                );
              })}
            </div>
          </div>

          {/* Repository File Tree */}
          <div className="flex-1">
            <span className="text-[10px] text-space-500 font-bold uppercase tracking-wider block mb-2 px-1">Eksplorasi Workspace</span>
            <div className="border border-space-800/30 bg-space-950/40 rounded-2xl p-2 max-h-[350px] overflow-y-auto scrollbar-thin">
              {renderFileTree(FILE_TREE_DATA)}
            </div>
          </div>
        </aside>

        {/* 📄 Right Content Area: Markdown & Document Viewer */}
        <main className="flex-1 bg-space-950 flex flex-col overflow-hidden">
          {/* Viewer Toolbar */}
          <div className="px-6 py-3 border-b border-space-800/40 bg-space-900/30 flex items-center justify-between shrink-0">
            <div className="flex items-center gap-2 truncate">
              <span className="text-[11px] font-mono text-space-500 uppercase">Path:</span>
              <span className="text-[11.5px] font-mono text-cosmic-cyan select-all truncate">{selectedPath}</span>
            </div>

            <div className="flex items-center gap-2">
              {/* Formatted vs Raw switch */}
              {(selectedPath.endsWith('.md') || selectedPath.endsWith('.sql') || selectedPath.endsWith('.json')) && (
                <div className="flex items-center bg-space-950 border border-space-800 p-0.5 rounded-lg text-[10.5px]">
                  <button
                    id="view-formatted"
                    onClick={() => setViewMode('formatted')}
                    className={`px-2.5 py-1 rounded-md transition-all ${viewMode === 'formatted' ? 'bg-space-800 text-white font-medium' : 'text-space-400 hover:text-white'}`}
                  >
                    Dokumen
                  </button>
                  <button
                    id="view-raw"
                    onClick={() => setViewMode('raw')}
                    className={`px-2.5 py-1 rounded-md transition-all ${viewMode === 'raw' ? 'bg-space-800 text-white font-medium' : 'text-space-400 hover:text-white'}`}
                  >
                    Kode Mentah
                  </button>
                </div>
              )}

              {/* Copy Action */}
              <button
                id="copy-btn"
                onClick={handleCopy}
                className="flex items-center gap-1.5 px-3 py-1.5 bg-space-800 hover:bg-space-700 active:scale-95 text-xs text-white border border-space-700 rounded-xl transition-all font-medium"
              >
                {copied ? (
                  <>
                    <CheckCircle2 size={13} className="text-cosmic-teal" />
                    <span className="text-cosmic-teal">Berhasil!</span>
                  </>
                ) : (
                  <>
                    <Copy size={13} className="text-space-300" />
                    <span>Salin Isi</span>
                  </>
                )}
              </button>
            </div>
          </div>

          {/* Document Display Canvas */}
          <div className="flex-1 overflow-y-auto p-8 scrollbar-thin">
            <div className="max-w-3xl mx-auto">
              <AnimatePresence mode="wait">
                <motion.div
                  key={selectedPath + viewMode}
                  initial={{ opacity: 0, y: 10 }}
                  animate={{ opacity: 1, y: 0 }}
                  exit={{ opacity: 0, y: -10 }}
                  transition={{ duration: 0.2 }}
                >
                  {viewMode === 'raw' || selectedPath.endsWith('.yml') || selectedPath.endsWith('.json') && !viewMode ? (
                    <div className="font-mono text-[11px] leading-relaxed bg-space-950 border border-space-800 p-6 rounded-2xl overflow-x-auto shadow-xl">
                      <pre className="text-emerald-400/90 whitespace-pre scrollbar-thin"><code>{activeContent}</code></pre>
                    </div>
                  ) : (
                    <div className="prose prose-invert prose-xs max-w-none">
                      {renderMarkdownContent(activeContent)}
                    </div>
                  )}
                </motion.div>
              </AnimatePresence>
            </div>
          </div>
        </main>
      </div>
    </div>
  );
}
