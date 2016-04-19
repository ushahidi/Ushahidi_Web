'''
Updated on Jan 11, 2010

@author: Kennedy Kasina
'''

import sys,os

class Ushahidi18nParser:
    
    def __init__(self): # constructor
        print "Ushahidi i18n Parser created by the Ushahidi Dev Team"
        
    
    def Usage(self):
        print 'Usage: Ushahidi i18n Parser\n\tpython uparser.py --{arguments}\n\n\tArguments'
        print '\t\t--genpot:\n\t\tuse this option to generate a pot file.'
        print '\n\t\te.g: python uparser.py --genpot {directory_with_php_files} {pot_filename}\n\n' 
        print '\t\t--getphp:\n\t\tuse this option to re-generate the php files with the translated'
        print '\t\tversion of the strings'
        print '\n\t\te.g: python uparser.py --genphp {pot_filename} {directory_to_regenerate}\n\n'
        print '\t\t--sync:\n\t\tuse this option to synchronize an old and current pot file\n\n'
        
    
    def StrippedLine(self, line):
        array = line.split("=>")
        line = array[1].strip().strip("',").strip()
        
        if line.endswith(":"): line = line.rstrip(":")
        if line.endswith("."): line = line.rstrip(".")
        if '\\' in line: line = line.replace("\\", "")
    
        return line

    
    def ReadLines(self, path):
        f = open(path, 'r')
        content = f.readlines()
        f.close()
        
        return content
    
    
    def WriteLines(self, path, content):
        # by default we delete the file before we write anything to it
        if os.path.exists(path): 
            os.remove(path)
            
        # recreate the file anew and append the content to it
        f = open(path, 'w')
        f.writelines(content)
        f.close()
    
        
    def Unlisted(self, list = [], line = ""):
        result = False
        line = line.lower()
        if line != "" and not line in list:
            list.append(line)
            result = True
        
        return result
    
    
    def GeneratePot(self, dir, pfile):
        temp = [ ]
        list = [ ]
        
        for root, dirs, files in os.walk(dir):
            for file in [f for f in files if f.endswith(".php")]:
                lines = self.ReadLines(os.path.join(root, file))
                
                for line in [l for l in lines if "=>" in l and not l.strip().endswith('array')]:
                    line = self.StrippedLine(line)
                    
                    if self.Unlisted(temp, line):
                        list.append(line)
        count = 0
        list.sort() # just the default list sorting algorithm 
        
        if os.path.exists(pfile): os.remove(pfile)
        f = open(pfile, 'w')
        for msgid in list:
            msg = msgid.replace('"','\\"')
            f.write('msgid "%s"\n'%msg)
            f.write('msgstr ""\n\n')
            count += 1
        f.close()
        
        return count


    def GeneratePHP(self, pfile, dir):
        temp = [ ]
        for line in self.ReadLines(pfile):
            if not line == "":
                temp.append(line)
        count = 0
        for root, dirs, files in os.walk(dir):
            for file in [f for f in files if f.endswith(".php")]:
                fn = os.path.join(root, file) # filename
                list = [ ]
                
                for line in self.ReadLines(fn):
                    if "=>" in line and not line.strip().endswith('array'):
                        item = self.StrippedLine(line)
                        msgid = ""
                        msgstr = ""
                        found = False
                        
                        for i in range(0, len(temp) - 1):
                            if temp[i].startswith("msgid"):
                                msgid  = temp[i].replace("msgid", "").strip().strip('"').strip()
                                msgstr = msgid # just incase the translation isnt available, retain original string
                                
                                if msgid.lower() == item.lower():
                                    found = True
                                    msgstr = temp[i+1].replace("msgstr", "").strip().strip('"').strip()
                                    break
                        
                        if found and msgid != "" and msgid != msgstr and msgid in line:
                            if msgid.lower() in line.lower():
                                if msgid.isupper(): # retain the original case
                                    msgstr = msgstr.upper()
                                line = line.replace(msgid, msgstr)
                                list.append(line)
                            count += 1
                    else:
                        list.append(line)
                        
                self.WriteLines(fn, list)       
        return count

    
    def SyncFiles(self, oldfile, newfile):
        list = [ ]
        ol = self.ReadLines(oldfile) # old list
        nl = self.ReadLines(newfile) # new list
        
        count = 0
        for i in range(len(nl) - 1):
            l = nl[i]
            if l.startswith("msgid"):
                msgid  = nl[i]
                msgstr = nl[i+1]

                for j in range(len(ol) - 1):
                    if ol[j] == msgid:
                        msgstr = ol[j+1]
                        count += 1
                        break
                msgstr += '\n\n'
                list.append(msgid)
                list.append(msgstr)
        
        self.WriteLines(newfile, list)
        return count
 

if __name__ == '__main__':
    i18n = Ushahidi18nParser()
    
    x = len(sys.argv)
    
    if x < 3:
        i18n.Usage()
    
    else:
        if sys.argv[1] == '--genpot': # --genpot path potfile
            print '\tGenerated a pot file with %s strings\n'%i18n.GeneratePot(sys.argv[2], sys.argv[3]) 
   
        elif sys.argv[i] == '--genphp': # --genphp potfile path
            print '\tRegenerated the php files with %s translated strings\n'%i18n.GeneratePHP(sys.argv[2], sys.argv[3])
          
        elif sys.argv[i] == '--sync': # --sync oldfile newfile
            print '\tSynced %s strings\n'%i18n.SyncFiles(sys.argv[2], sys.argv[3])

        else:
            print "Error: Unknown argument\n"
            i18n.Usage()
