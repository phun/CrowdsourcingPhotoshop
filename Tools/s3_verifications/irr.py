import sys, re

data1 = []
data2 = []

# load the files
for (filename,data) in zip(sys.argv[1:], (data1, data2)):
	print "reading from " + filename
	with open(filename) as f:
		for line in f.readlines():
			for (cid,before,after) in re.findall(r"cid[^}]*?(\d+)[^}]*?before[^}]*?(true|false)[^}]*?after[^}]*?(true|false)", line):
				data.append({"cid":cid, "before": before=="true", "after": after=="true"})
	print data
			
# now data1 and data2 are each a list of records of the form:
# {"cid":"14","before":false,"after":true}

matrix = {"TT": 0, "TF": 0, "FT": 0, "FF": 0}
for entry1 in data1:
	for entry2 in data2:
		if entry1["cid"] == entry2["cid"]:
			if entry1["before"] and entry2["before"]:
				matrix["TT"] += 1
			elif entry1["before"] and not entry2["before"]:
				matrix["TF"] += 1
			elif not entry1["before"] and entry2["before"]:
				matrix["FT"] += 1
			elif not entry1["before"] and not entry2["before"]:
				matrix["FF"] += 1
				
			if entry1["after"] and entry2["after"]:
				matrix["TT"] += 1
			elif entry1["after"] and not entry2["after"]:
				matrix["TF"] += 1
			elif not entry1["after"] and entry2["after"]:
				matrix["FT"] += 1
			elif not entry1["after"] and not entry2["after"]:
				matrix["FF"] += 1	
				
print matrix

# now compute Cohen's Kappa
diagonal = sum(matrix[cond] for cond in matrix if cond is "TT" or cond is "FF")
total = sum(matrix[cond] for cond in matrix)
kappa = 1.0*diagonal / total

print "Cohen's Kappa:", kappa
