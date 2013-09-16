import sys
import math
from matching_text import is_same_label
from matching_image import is_same_image

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
        datum["count"] = split_str[0]
        datum["workerid"] = split_str[1]
        datum["video_id"] = split_str[2]
        datum["slug"] = split_str[3]
        datum["clusterid"] = split_str[4]        
        datum["video_full_id"] = split_str[5]
        datum["time"] = split_str[6]
        datum["label"] = split_str[7]
        datum["before"] = split_str[8]
        datum["after"] = split_str[9]
        datum["before_noop"] = split_str[10]
        datum["after_noop"] = split_str[11]
        if datum["clusterid"] not in data:
            data[datum["clusterid"]] = []
        data[datum["clusterid"]].append(datum)
    f.close()
    return data


# Save JSON files of final clusters
def save_files(final_data):
    import json
    with open(sys.argv[1] + '.final.json', 'wb') as fp:
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
    if label[mode] == "@":
        answer = label["instruction"]
    elif label[mode] == "noop":
        answer = "noop"
    else:
        label_list = label["labels"].split("====")
        answer = label_list[int(label[mode])].rstrip('\n')
    return answer


''' Return file path to thumbnail '''
def getImage(label, mode):
    #{0:03d}".format(number)
    return "./thumbs/v_" + label["slug"] + "_" + label[mode].zfill(3) + ".jpg" 


''' analyze before and after data. 
    mode is either "before" or "after"
'''
def analyze_data(mode, l1, l2, l3):
    global count_noop
    global count_unanimous
    global count_unanimous_noop_included
    global count_majority
    global count_majority_noop_included 
    global count_majority_dup_string
    global count_diverse
    global count_diverse_noop_included 
    global count_diverse_dup_string 
    global count_else

    final_label = ""
    try:
        # print l1["vid"], cid
        if l1[mode + "_noop"] == "on" and l2[mode + "_noop"] == "on" and l3[mode + "_noop"] == "on":
            final_label = "noop"
            count_unanimous += 1
            count_unanimous_noop_included += 1
        elif l1[mode] == l2[mode] == l3[mode]:
            # print "[unanimous]", getImage(l1, mode)
            final_label = l1[mode]
            count_unanimous += 1
        elif (l1[mode + "_noop"] == "on" and l2[mode + "_noop"] == "on") or (l2[mode + "_noop"] == "on" and l3[mode + "_noop"] == "on") or (l3[mode + "_noop"] == "on" and l1[mode + "_noop"] == "on"):
            final_label = "noop"
            count_majority_noop_included += 1
            count_majority += 1
        elif (l1[mode] == l2[mode]) or (l2[mode] == l3[mode]) or (l3[mode] == l1[mode]):
            if l1[mode] == l2[mode]:
                if is_same_image(getImage(l1, mode), getImage(l3, mode))[0]:
                    count_majority_dup_string += 1
                answer_label = l1
            elif l2[mode] == l3[mode]:
                if is_same_image(getImage(l2, mode), getImage(l1, mode))[0]:
                    count_majority_dup_string += 1            
                answer_label = l2   
            elif l3[mode] == l1[mode]:
                if is_same_image(getImage(l3, mode), getImage(l2, mode))[0]:
                    count_majority_dup_string += 1
                answer_label = l3
            # print "[majority]", getImage(answer_label, mode)
            final_label = answer_label[mode]
            count_majority += 1
        elif (l1[mode] != l2[mode]) and (l2[mode] != l3[mode]) and (l3[mode] != l1[mode]):
            # print "[diverse]", getImage(l1, mode), "==", getImage(l2, mode), "==", getImage(l3, mode)   
            s12 = is_same_image(getImage(l1, mode), getImage(l2, mode))
            s23 = is_same_image(getImage(l2, mode), getImage(l3, mode))
            s31 = is_same_image(getImage(l3, mode), getImage(l1, mode))
            if s12[0]:
                count_diverse_dup_string += 1
                final_label = l1[mode]  # pick any for image
            elif s23[0]:
                count_diverse_dup_string += 1
                final_label = l2[mode]
            elif s31[0]:
                count_diverse_dup_string += 1
                final_label = l3[mode]
            else:
                if l1[mode + "_noop"] == "on" or l2[mode + "_noop"] == "on" or l3[mode + "_noop"] == "on":
                    count_diverse_noop_included += 1
                    # todo: test with noop
                    # final_label = "noop"
                    if l1[mode + "_noop"] == "on":
                        final_label = l2[mode]
                    if l2[mode + "_noop"] == "on":
                        final_label = l3[mode]
                    if l3[mode + "_noop"] == "on":
                        final_label = l1[mode]                                                
                # let's use the longest string if we cannot resolve
                # final_label = max([getImage(l1, mode), getImage(l2, mode), getImage(l3, mode)], key=len)
                # print "no match", l1[mode], l2[mode], l3[mode]
                # final_label = "noop"
            count_diverse += 1
        else:
            print "something else"
            count_else += 1
    except IOError:
        print "file does not exist"

    return final_label


''' 
    Post-Process stage 2 results
    Usage 
        python dbscan.py [turk_filename]
    Parameters
    - turk_filename: e.g., "s3.data"
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
count_majority = 0
count_majority_noop_included = 0
count_majority_dup_string = 0
count_diverse = 0
count_diverse_noop_included = 0
count_diverse_dup_string = 0
count_else = 0

for cid in cluster_list:
    cluster = clusters[cid]
    l1 = cluster[0]
    l2 = cluster[1]
    l3 = cluster[2]
    print "[", l1["video_full_id"], "]"
    final_before = analyze_data("before", l1, l2, l3)
    final_after = analyze_data("after", l1, l2, l3)

    print "[final]", final_before, final_after
    count_noop = count_unanimous_noop_included + count_majority_noop_included + count_diverse_noop_included

    vid = l1["video_full_id"][:-4]
    # make the stage prefix match
    vid = vid[:1] + "1" + vid[2:]
    if vid not in final_data:
        final_data[vid] = {}
    final_data[vid][cid] = {"cluster_id": cid, "time": l1["time"], "before_index": final_before, "after_index": final_after}
    # datum["id"] = split_str[0]
    # datum["workerid"] = split_str[1]
    # datum["clusterid"] = split_str[2]
    # datum[mode] = split_str[3]
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



# vidlist = final_data.keys()
# vidlist.sort()
# window_size = 5
# for vid in vidlist:
#     print vid
#     sorted_labels = []
#     for lid in final_data[vid]:
#         sorted_labels.append({'lid': lid, 'time': final_data[vid][lid]["time"]})         
#     sorted_labels = sorted(sorted_labels, key=lambda k: float(k["time"]))
#     sorted_lidlist = [item["lid"] for item in sorted_labels]    

#     prev_label = {}
#     for lid in sorted_lidlist:
#         cur_label = final_data[vid][lid]
#         print "  ", lid, cur_label["time"], cur_label["label"]
#         # look for potential merge, if label is same and time is close enough
#         if prev_label:
#             distance = math.fabs(float(prev_label["time"]) - float(cur_label["time"]))
#             text_sim = is_same_image(prev_label["label"], cur_label["label"])
#             if distance <= window_size and text_sim[3] > 0.8:
#                 print " [merging]", text_sim[3], prev_label["time"], cur_label["time"], prev_label["label"], cur_label["label"]
#                 del final_data[vid][lid]
#         prev_label = cur_label

# save_files(final_data)

# print final_data
# print len(final_data)
print "STATS"
print "unanimous:", count_unanimous, "noop", count_unanimous_noop_included
print "majority:", count_majority, "noop", count_majority_noop_included, "dup", count_majority_dup_string
print "diverse:", count_diverse, "noop", count_diverse_noop_included, "dup", count_diverse_dup_string
print "noop:", count_noop



