import re
import os
import glob
import json
import tkinter as tk
from tkinter import ttk
from tkinter import filedialog, messagebox, simpledialog
"""
TranslatorApp: A small GUI application for managing JSON-based translation files.
Author: Krzysztof Pacyna & ChatGPT
Python Version: 3.8+
Date: 2024-10-29
"""
class TranslatorApp:
    def __init__(self, root):
        self.root = root
        self.root.title("Translator")
        self.data = {}
        self.load_files()
        self.create_table()
        self.create_buttons()
        self.root.bind("<Key>", self.jump_to_key)
    def load_files(self):
        for file_name in glob.glob("*.json"):
            try:
                with open(file_name, "r", encoding="utf-8") as f:
                    json_text = self.clean_json(f.read())
                    self.data[file_name] = json.loads(json_text)
            except (json.JSONDecodeError, FileNotFoundError):
                self.data[file_name] = {}
                messagebox.showerror("Error", f"Failed to load file: {file_name}")
    def clean_json(self, json_text):
        return re.sub(r",\s*([}\]])", r"\1", json_text)
    def create_table(self):
        self.tree = ttk.Treeview(self.root, show="headings")
        self.tree.pack(fill="both", expand=True)
        self.update_table_headers()
        self.refresh_table()
        self.tree.bind("<Double-1>", self.on_double_click)
    def update_table_headers(self):
        self.tree["columns"] = ("Key",) + tuple(self.data.keys())
        for col in self.tree["columns"]:
            self.tree.heading(col, text=col)
    def refresh_table(self):
        self.tree.delete(*self.tree.get_children())
        sorted_keys = sorted(set(key for lang in self.data.values() for key in lang.keys()))
        for key in sorted_keys:
            values = [key] + [self.data[lang].get(key, "") for lang in self.data.keys()]
            self.tree.insert("", "end", values=values)
    def on_double_click(self, event):
        item_id = self.tree.selection()[0]
        EditWindow(self, item_id, self.tree.item(item_id, "values"))
    def update_value(self, item_id, new_key, new_values):
        old_key = self.tree.item(item_id, "values")[0]
        if old_key != new_key:
            for lang in self.data.keys():
                self.data[lang][new_key] = self.data[lang].pop(old_key, "")
        self.tree.item(item_id, values=(new_key,) + tuple(new_values))
        for i, lang in enumerate(self.data.keys()):
            if i < len(new_values) and new_values[i]:
                self.data[lang][new_key] = new_values[i]
            else:
                self.data[lang].pop(new_key, None)
        self.refresh_table()
    def create_buttons(self):
        button_frame = tk.Frame(self.root)
        button_frame.pack(pady=5)
        tk.Button(button_frame, text="Add Language", command=self.add_language).pack(side="left", padx=5)
        tk.Button(button_frame, text="Remove Language", command=self.remove_language).pack(side="left", padx=5)
        tk.Button(button_frame, text="Add New Key", command=self.add_key).pack(side="left", padx=5)
        tk.Button(button_frame, text="Save Files", command=self.save_files).pack(side="left", padx=5)
    def add_key(self):
        new_key = simpledialog.askstring("Add New Key", "Enter key name:")
        if new_key:
            new_values = [simpledialog.askstring("Enter Translation", f"Translation for {lang}:") or ""
                          for lang in self.data.keys()]
            for i, lang in enumerate(self.data.keys()):
                self.data[lang][new_key] = new_values[i]
            self.refresh_table()
    def add_language(self):
        lang_name = simpledialog.askstring("Add Language", "Enter file name (e.g., fr.json):")
        if lang_name and lang_name.endswith(".json") and lang_name not in self.data:
            self.data[lang_name] = {}
            self.update_table_headers()
            self.refresh_table()
        else:
            messagebox.showerror("Error", "Language already exists or invalid file name.")
    def remove_language(self):
        lang_name = simpledialog.askstring("Remove Language", "Enter file name (e.g., fr.json):")
        if lang_name in self.data:
            del self.data[lang_name]
            self.update_table_headers()
            self.refresh_table()
        else:
            messagebox.showerror("Error", "Language does not exist.")
    def save_files(self):
        try:
            for lang, translations in self.data.items():
                data_to_save = {key: val for key, val in translations.items() if val}
                with open(lang, "w", encoding="utf-8") as f:
                    json.dump(data_to_save, f, ensure_ascii=False, indent=4)
            messagebox.showinfo("Success", "Files saved successfully.")
        except Exception as e:
            messagebox.showerror("Error", f"Problem saving files: {e}")
    def jump_to_key(self, event):
        letter = event.char.lower()
        for item in self.tree.get_children():
            key = self.tree.item(item, "values")[0].lower()
            if key.startswith(letter):
                self.tree.selection_set(item)
                self.tree.see(item)
                break
class EditWindow:
    def __init__(self, app, item_id, values):
        self.app = app
        self.item_id = item_id
        self.values = values
        self.window = tk.Toplevel()
        self.window.title(f"Edit {values[0]}")
        tk.Label(self.window, text="Key:").grid(row=0, column=0)
        self.key_entry = tk.Entry(self.window, width=30)
        self.key_entry.grid(row=0, column=1)
        self.key_entry.insert(0, values[0])
        self.entries = []
        for i, lang in enumerate(self.app.data.keys()):
            tk.Label(self.window, text=lang).grid(row=i + 1, column=0)
            entry = tk.Entry(self.window, width=30)
            entry.grid(row=i + 1, column=1)
            entry.insert(0, values[i + 1])
            self.entries.append(entry)
        tk.Button(self.window, text="Save", command=self.save).grid(row=len(self.app.data) + 1, column=0)
        tk.Button(self.window, text="Delete", command=self.delete).grid(row=len(self.app.data) + 1, column=1)
    def save(self):
        new_key = self.key_entry.get()
        new_values = [entry.get() for entry in self.entries]
        self.app.update_value(self.item_id, new_key, new_values)
        self.window.destroy()
    def delete(self):
        key = self.values[0]
        for lang in self.app.data.keys():
            self.app.data[lang].pop(key, None)
        self.app.refresh_table()
        self.window.destroy()
if __name__ == "__main__":
    root = tk.Tk()
    app = TranslatorApp(root)
    root.mainloop()
