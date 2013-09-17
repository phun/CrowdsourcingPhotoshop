import sys
import math
from matching_text import is_same_label

##############################################################################
# Read Turker data file
def read_turk_data(filename):
    data = {}
    f = open(filename)
    lines = f.readlines()
    datum = {}
    for index, line in enumerate(lines):
        split_str = line.split('\t\t')
        # print split_str
        datum = {}
        datum["id"] = split_str[0]
        datum["workerid"] = split_str[1]
        datum["clusterid"] = split_str[2]
        datum["answer"] = split_str[3]
        datum["instruction"] = split_str[4]
        datum["order"] = split_str[5]
        datum["vid"] = split_str[6]
        datum["time"] = split_str[7]
        # print split_str[2].split('====')
        datum["labels"] = split_str[8]
        # datum["vid"] = split_str[5][:-4] # e.g., "s1_c02_v02" and "s04" from "s1_c02_v02_s04"
        # datum["labels"] = parse_labels(datum["workerid"], int(split_str[2][-2:]), split_str[3].rstrip('\n'))
        # if datum["vid"].startswith("s2_p03_v03"):
        #     print datum["instruction"] in datum["order"].split(',')
        #     print datum
        if datum["clusterid"] not in data:
            data[datum["clusterid"]] = []
        data[datum["clusterid"]].append(datum)
    f.close()
    return data


# Save JSON files of final clusters
def save_files(final_data):
    import json
    with open(sys.argv[1] + '.nomerge.test.final.json', 'wb') as fp:
        #json.dump(final_data, fp)
        json.dump(final_data, fp, sort_keys=True, indent=4, separators=(',', ': '))


def compare_int_keys(x, y):
    x = int(x)
    y = int(y)
    return cmp(x,y)


''' for the given Turker input, return the Turker's answer in text
'''
def getLabel(label):
    answer = ""
    if label["answer"] == "@":
        answer = label["instruction"]
    elif label["answer"] == "noop":
        answer = "noop"
    else:
        label_list = label["labels"].split("====")
        answer = label_list[int(label["answer"])].rstrip('\n')
    return answer


''' 
    Post-Process stage 2 results
    Usage 
        python dbscan.py [turk_filename]
    Parameters
    - turk_filename: e.g., "s2.data"
'''
turk_filename = sys.argv[1]
# truth_filename = sys.argv[2]
# read and parse data
clusters = read_turk_data(turk_filename)
# truth = read_truth_data(truth_filename)
cluster_list = clusters.keys()
cluster_list = sorted(cluster_list, cmp=compare_int_keys)

# fp = open(turk_filename + ".parsed", 'wb')

final_data = {}

count_noop = 0
count_unanimous = 0
count_unanimous_noop_included = 0
count_unanimous_custom_included = 0
count_majority = 0
count_majority_noop_included = 0
count_majority_dup_string = 0
count_majority_custom_included = 0
count_diverse = 0
count_diverse_noop_included = 0
count_diverse_dup_string = 0
count_diverse_custom_included = 0
count_else = 0

for cid in cluster_list:
    cluster = clusters[cid]
    l1 = cluster[0]
    l2 = cluster[1]
    l3 = cluster[2]
    final_label = ""
    # print l1["vid"], cid
    if l1["answer"] == l2["answer"] == l3["answer"]:
        # print "[unanimous]", getLabel(l1)
        final_label = getLabel(l1)
        count_unanimous += 1
        if l1["answer"] == "noop":
            count_unanimous_noop_included += 1
        if l1["answer"] == "@":
            count_unanimous_custom_included += 1
    elif (l1["answer"] == l2["answer"]) or (l2["answer"] == l3["answer"]) or (l3["answer"] == l1["answer"]):
        if l1["answer"] == l2["answer"]:
            if l1["answer"] == "@":
                count_majority_custom_included += 1
            if is_same_label(getLabel(l1), getLabel(l3))[0]:
                count_majority_dup_string += 1
            answer_label = l1
            if l1["answer"] == "noop":
                count_majority_noop_included += 1    
        elif l2["answer"] == l3["answer"]:
            if l2["answer"] == "@":
                count_majority_custom_included += 1
            if is_same_label(getLabel(l2), getLabel(l1))[0]:
                count_majority_dup_string += 1            
            answer_label = l2
            if l2["answer"] == "noop":
                count_majority_noop_included += 1    
        elif l3["answer"] == l1["answer"]:
            if l3["answer"] == "@":
                count_majority_custom_included += 1
            if is_same_label(getLabel(l3), getLabel(l2))[0]:
                count_majority_dup_string += 1
            answer_label = l3
            if l3["answer"] == "noop":
                count_majority_noop_included += 1
        # print "[majority]", getLabel(answer_label)
        final_label = getLabel(answer_label)
        count_majority += 1
    elif (l1["answer"] != l2["answer"]) and (l2["answer"] != l3["answer"]) and (l3["answer"] != l1["answer"]):
        # print "[diverse]", getLabel(l1), "==", getLabel(l2), "==", getLabel(l3)
        if l1["answer"] == "@" or l2["answer"] == "@" or l3["answer"] == "@":
            count_diverse_custom_included += 1      
        s12 = is_same_label(getLabel(l1), getLabel(l2))
        s23 = is_same_label(getLabel(l2), getLabel(l3))
        s31 = is_same_label(getLabel(l3), getLabel(l1))
        if s12[0]:
            count_diverse_dup_string += 1
            final_label = s12[1]
        elif s23[0]:
            count_diverse_dup_string += 1
            final_label = s23[1]
        elif s31[0]:
            count_diverse_dup_string += 1
            final_label = s31[1]
        else:
            # let's use the longest string if we cannot resolve
            final_label = max([getLabel(l1), getLabel(l2), getLabel(l3)], key=len)
            # final_label = "noop"

        if l1["answer"] == "noop" or l2["answer"] == "noop" or l3["answer"] == "noop":
            count_diverse_noop_included += 1
            final_label = max([getLabel(l1), getLabel(l2), getLabel(l3)], key=len)
            # final_label = "noop"
        
        count_diverse += 1
    else:
        print "something else"
        count_else += 1

    count_noop = count_unanimous_noop_included + count_majority_noop_included + count_diverse_noop_included

    vid = l1["vid"][:-4]
    # make the stage prefix match
    vid = vid[:1] + "1" + vid[2:]
    if vid not in final_data:
        final_data[vid] = {}
    final_data[vid][cid] = {"cluster_id": cid, "time": l1["time"], "label": final_label}
    # datum["id"] = split_str[0]
    # datum["workerid"] = split_str[1]
    # datum["clusterid"] = split_str[2]
    # datum["answer"] = split_str[3]
    # datum["instruction"] = split_str[4]
    # datum["order"] = split_str[5]
    # datum["vid"] = split_str[6]
    # # print split_str[2].split('====')
    # datum["labels"] = split_str[7]

    # print "\n" 
    # sorted_turk = sorted(turk[cid], key=lambda tup: tup["time"])
    # print vid, "Turk:", len(turk[vid]), "Truth:", len(truth[vid])#, data[vid]
    # only grab time information from the data

    # fp.write("===" + vid + " " + str(eps) + "===\n")

    # cluster_data = {}
    # for (i, label) in enumerate(label_turk_list):
    #     key = str(clusters[i])
    #     entry = {'cluster_id': key, 'time': label, 'workerid': sorted_turk[i]["workerid"], 'desc': sorted_turk[i]["desc"]}
    #     if key not in cluster_data:
    #         cluster_data[key] = []
    #     cluster_data[key].append(entry)
    #     line = str(label) + " (" + clusters[i] + ") " + sorted_turk[i]["workerid"] + " " + sorted_turk[i]["desc"]
    #     fp.write(line + "\n")
    #     print label, "(", clusters[i], ")", sorted_turk[i]["workerid"], sorted_turk[i]["desc"]

    # final_data[vid] = {}
    # for cid in cluster_data:
    #     final_data[vid][cid] = {'cluster_id': cid, 'time': getClusterTime(cluster_data[cid], cid), 'points_turk': cluster_data[cid]}
    #     # print "---"
    #     # print final_data[vid][cid]

do_merging = True
if do_merging:
    vidlist = final_data.keys()
    vidlist.sort()
    window_size = 5
    for vid in vidlist:
        print vid
        sorted_labels = []
        for lid in final_data[vid]:
            sorted_labels.append({'lid': lid, 'time': final_data[vid][lid]["time"]})         
        sorted_labels = sorted(sorted_labels, key=lambda k: float(k["time"]))
        sorted_lidlist = [item["lid"] for item in sorted_labels]    

        prev_label = {}
        for lid in sorted_lidlist:
            cur_label = final_data[vid][lid]
            print "  ", lid, cur_label["time"], cur_label["label"]
            # look for potential merge, if label is same and time is close enough
            if prev_label:
                distance = math.fabs(float(prev_label["time"]) - float(cur_label["time"]))
                text_sim = is_same_label(prev_label["label"], cur_label["label"])
                if distance <= window_size and text_sim[3] > 0.8:
                    print " [merging]", text_sim[3], prev_label["time"], cur_label["time"], prev_label["label"], cur_label["label"]
                    del final_data[vid][lid]
            prev_label = cur_label

save_files(final_data)

# print final_data
# print len(final_data)
print "STATS"
print "unanimous:", count_unanimous, "noop", count_unanimous_noop_included, "custom", count_unanimous_custom_included
print "majority:", count_majority, "noop", count_majority_noop_included, "dup", count_majority_dup_string, "custom", count_majority_custom_included
print "diverse:", count_diverse, "noop", count_diverse_noop_included, "dup", count_diverse_dup_string, "custom", count_diverse_custom_included
print "noop:", count_noop



