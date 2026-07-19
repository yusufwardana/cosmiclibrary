/**
 * @license
 * SPDX-License-Identifier: Apache-2.0
 */

export interface DocFile {
  path: string;
  title: string;
  purpose: string;
  content: string;
  category: 'system' | 'engine' | 'utility' | 'blueprint' | 'prompt' | 'workflow';
}

export interface FileNode {
  name: string;
  path: string;
  type: 'file' | 'directory';
  children?: FileNode[];
  content?: string;
  isDoc?: boolean;
}

export interface ProjectStat {
  label: string;
  value: string | number;
  icon: string;
  description: string;
}
